<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGameRequest;
use App\Http\Requests\Admin\UpdateGameRequest;
use App\Models\Game;
use App\Models\PlaystationUnit;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class GameController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', $request->input('search', '')));
        $active = (string) $request->input('active', '');
        $platform = trim((string) $request->input('platform', ''));

        $query = Game::query()->withCount('units');

        $query->when($search !== '', function (Builder $query) use ($search): void {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('genre', 'like', "%{$search}%")
                    ->orWhere('platform', 'like', "%{$search}%");
            });
        });

        if ($active === 'active' || $active === '1') {
            $query->where('is_active', true);
        } elseif ($active === 'inactive' || $active === '0') {
            $query->where('is_active', false);
        }

        $query->when($platform !== '', fn (Builder $query) => $query->where('platform', $platform));

        $games = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.games.index', [
            'games' => $games,
            'platforms' => Game::query()
                ->whereNotNull('platform')
                ->where('platform', '!=', '')
                ->distinct()
                ->orderBy('platform')
                ->pluck('platform'),
        ]);
    }

    public function create(): View
    {
        return view('admin.games.create', [
            'units' => $this->units(),
        ]);
    }

    public function store(StoreGameRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $newCover = null;

        try {
            if ($request->hasFile('cover')) {
                $newCover = $request->file('cover')->store('games', 'public');

                if (! $newCover) {
                    throw new RuntimeException('The game cover could not be stored.');
                }

                $data['cover'] = $newCover;
            }

            $unitIds = $data['unit_ids'] ?? [];
            unset($data['unit_ids']);

            DB::transaction(function () use ($data, $unitIds): void {
                $game = Game::create($data);
                $game->units()->sync($unitIds);
            });
        } catch (Throwable $exception) {
            if ($newCover) {
                $this->deleteCover($newCover);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Game gagal disimpan. Silakan coba lagi.');
        }

        return redirect()
            ->route('admin.games.index')
            ->with('success', 'Game berhasil ditambahkan.');
    }

    public function edit(Game $game): View
    {
        return view('admin.games.edit', [
            'game' => $game->load('units:id,code,name'),
            'units' => $this->units(),
        ]);
    }

    public function update(UpdateGameRequest $request, Game $game): RedirectResponse
    {
        $data = $request->validated();
        $oldCover = $game->cover;
        $newCover = null;

        if (! $request->hasFile('cover')) {
            unset($data['cover']);
        }

        try {
            if ($request->hasFile('cover')) {
                $newCover = $request->file('cover')->store('games', 'public');

                if (! $newCover) {
                    throw new RuntimeException('The game cover could not be stored.');
                }

                $data['cover'] = $newCover;
            }

            $unitIds = $data['unit_ids'] ?? [];
            unset($data['unit_ids']);

            DB::transaction(function () use ($game, $data, $unitIds): void {
                $game->update($data);
                $game->units()->sync($unitIds);
            });
        } catch (Throwable $exception) {
            if ($newCover) {
                $this->deleteCover($newCover);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Game gagal diperbarui. Silakan coba lagi.');
        }

        if ($newCover && $oldCover !== $newCover) {
            $this->deleteCover($oldCover);
        }

        return redirect()
            ->route('admin.games.index')
            ->with('success', 'Game berhasil diperbarui.');
    }

    public function destroy(Game $game): RedirectResponse
    {
        $cover = $game->cover;

        try {
            DB::transaction(function () use ($game): void {
                $game->delete();
            });
        } catch (QueryException $exception) {
            report($exception);

            return redirect()
                ->route('admin.games.index')
                ->with('error', 'Game tidak dapat dihapus karena masih memiliki data terkait.');
        }

        $this->deleteCover($cover);

        return redirect()
            ->route('admin.games.index')
            ->with('success', 'Game berhasil dihapus.');
    }

    /** @return Collection<int, PlaystationUnit> */
    private function units(): Collection
    {
        return PlaystationUnit::query()
            ->with('type:id,name')
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'playstation_type_id']);
    }

    private function deleteCover(mixed $path): void
    {
        if (
            ! is_string($path)
            || $path === ''
            || ! str_starts_with($path, 'games/')
            || str_contains($path, '..')
        ) {
            return;
        }

        try {
            Storage::disk('public')->delete($path);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
