<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UnitStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlaystationUnitRequest;
use App\Http\Requests\Admin\UpdatePlaystationUnitRequest;
use App\Models\PlaystationType;
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

class PlaystationUnitController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', $request->input('search', '')));
        $status = UnitStatus::tryFrom((string) $request->input('status', ''));
        $typeId = $request->input('type_id');
        $active = (string) $request->input('active', '');

        $query = PlaystationUnit::query()->with('type');

        $query->when($search !== '', function (Builder $query) use ($search): void {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        });

        $query->when($status, fn (Builder $query) => $query->where('status', $status->value));
        $query->when(
            is_numeric($typeId) && (int) $typeId > 0,
            fn (Builder $query) => $query->where('playstation_type_id', (int) $typeId),
        );

        if ($active === 'active' || $active === '1') {
            $query->where('is_active', true);
        } elseif ($active === 'inactive' || $active === '0') {
            $query->where('is_active', false);
        }

        $units = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.units.index', [
            'units' => $units,
            'types' => $this->types(),
            'statuses' => UnitStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.units.create', [
            'types' => $this->types(),
        ]);
    }

    public function store(StorePlaystationUnitRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $newPhoto = null;

        try {
            if ($request->hasFile('photo')) {
                $newPhoto = $request->file('photo')->store('units', 'public');

                if (! $newPhoto) {
                    throw new RuntimeException('The unit photo could not be stored.');
                }

                $data['photo'] = $newPhoto;
            }

            DB::transaction(function () use ($data): void {
                PlaystationUnit::create($data);
            });
        } catch (Throwable $exception) {
            if ($newPhoto) {
                $this->deletePhoto($newPhoto);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Unit PlayStation gagal disimpan. Silakan coba lagi.');
        }

        return redirect()
            ->route('admin.units.index')
            ->with('success', 'Unit PlayStation berhasil ditambahkan.');
    }

    public function edit(PlaystationUnit $unit): View
    {
        return view('admin.units.edit', [
            'unit' => $unit,
            'types' => $this->types(),
        ]);
    }

    public function update(
        UpdatePlaystationUnitRequest $request,
        PlaystationUnit $unit,
    ): RedirectResponse {
        $data = $request->validated();
        $oldPhoto = $unit->photo;
        $newPhoto = null;

        if (! $request->hasFile('photo')) {
            unset($data['photo']);
        }

        try {
            if ($request->hasFile('photo')) {
                $newPhoto = $request->file('photo')->store('units', 'public');

                if (! $newPhoto) {
                    throw new RuntimeException('The unit photo could not be stored.');
                }

                $data['photo'] = $newPhoto;
            }

            DB::transaction(function () use ($unit, $data): void {
                $unit->update($data);
            });
        } catch (Throwable $exception) {
            if ($newPhoto) {
                $this->deletePhoto($newPhoto);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Unit PlayStation gagal diperbarui. Silakan coba lagi.');
        }

        if ($newPhoto && $oldPhoto !== $newPhoto) {
            $this->deletePhoto($oldPhoto);
        }

        return redirect()
            ->route('admin.units.index')
            ->with('success', 'Unit PlayStation berhasil diperbarui.');
    }

    public function destroy(PlaystationUnit $unit): RedirectResponse
    {
        if ($unit->bookings()->exists() || $unit->schedules()->exists()) {
            return redirect()
                ->route('admin.units.index')
                ->with('error', 'Unit tidak dapat dihapus karena masih memiliki booking atau jadwal.');
        }

        $photo = $unit->photo;

        try {
            DB::transaction(function () use ($unit): void {
                $unit->delete();
            });
        } catch (QueryException $exception) {
            report($exception);

            return redirect()
                ->route('admin.units.index')
                ->with('error', 'Unit tidak dapat dihapus karena masih memiliki data terkait.');
        }

        $this->deletePhoto($photo);

        return redirect()
            ->route('admin.units.index')
            ->with('success', 'Unit PlayStation berhasil dihapus.');
    }

    private function types(): Collection
    {
        return PlaystationType::query()
            ->orderBy('name')
            ->get();
    }

    private function deletePhoto(mixed $path): void
    {
        if (
            ! is_string($path)
            || $path === ''
            || ! str_starts_with($path, 'units/')
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
