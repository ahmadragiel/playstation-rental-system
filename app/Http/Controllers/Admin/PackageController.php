<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePackageRequest;
use App\Http\Requests\Admin\UpdatePackageRequest;
use App\Models\Package;
use App\Models\PlaystationType;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class PackageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', $request->input('search', '')));
        $typeId = $request->input('type_id');
        $active = (string) $request->input('active', '');
        $featured = (string) $request->input('featured', '');

        $query = Package::query()->with('type');

        $query->when($search !== '', function (Builder $query) use ($search): void {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('type', function (Builder $query) use ($search): void {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        });

        $query->when(
            is_numeric($typeId) && (int) $typeId > 0,
            fn (Builder $query) => $query->where('playstation_type_id', (int) $typeId),
        );

        if ($active === 'active' || $active === '1') {
            $query->where('is_active', true);
        } elseif ($active === 'inactive' || $active === '0') {
            $query->where('is_active', false);
        }

        if ($featured === 'featured' || $featured === '1') {
            $query->where('is_featured', true);
        } elseif ($featured === 'not_featured' || $featured === '0') {
            $query->where('is_featured', false);
        }

        $packages = $query
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.packages.index', [
            'packages' => $packages,
            'types' => $this->types(),
        ]);
    }

    public function create(): View
    {
        return view('admin.packages.create', [
            'types' => $this->types(),
        ]);
    }

    public function store(StorePackageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['facilities'] = $this->normaliseFacilities($data['facilities'] ?? null);

        try {
            DB::transaction(function () use ($data): void {
                Package::create($data);
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Paket sewa gagal disimpan. Silakan coba lagi.');
        }

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Paket sewa berhasil ditambahkan.');
    }

    public function edit(Package $package): View
    {
        return view('admin.packages.edit', [
            'package' => $package,
            'types' => $this->types(),
        ]);
    }

    public function update(UpdatePackageRequest $request, Package $package): RedirectResponse
    {
        $data = $request->validated();
        $data['facilities'] = $this->normaliseFacilities($data['facilities'] ?? null);

        try {
            DB::transaction(function () use ($package, $data): void {
                $package->update($data);
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Paket sewa gagal diperbarui. Silakan coba lagi.');
        }

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Paket sewa berhasil diperbarui.');
    }

    public function destroy(Package $package): RedirectResponse
    {
        if ($package->bookings()->exists()) {
            return redirect()
                ->route('admin.packages.index')
                ->with('error', 'Paket tidak dapat dihapus karena masih memiliki booking.');
        }

        try {
            DB::transaction(function () use ($package): void {
                $package->delete();
            });
        } catch (QueryException $exception) {
            report($exception);

            return redirect()
                ->route('admin.packages.index')
                ->with('error', 'Paket tidak dapat dihapus karena masih memiliki data terkait.');
        }

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Paket sewa berhasil dihapus.');
    }

    private function types(): Collection
    {
        return PlaystationType::query()
            ->orderBy('name')
            ->get();
    }

    private function normaliseFacilities(mixed $facilities): ?array
    {
        if ($facilities === null || $facilities === '') {
            return null;
        }

        if (is_string($facilities)) {
            $facilities = preg_split('/[\r\n,]+/', $facilities) ?: [];
        }

        if (! is_array($facilities)) {
            return null;
        }

        $items = [];
        foreach ($facilities as $facility) {
            if (! is_scalar($facility)) {
                continue;
            }

            $facility = trim((string) $facility);
            if ($facility !== '') {
                $items[] = $facility;
            }
        }

        $items = array_values(array_unique($items));

        return $items === [] ? null : $items;
    }
}
