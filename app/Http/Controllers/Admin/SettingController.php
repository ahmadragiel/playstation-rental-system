<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * @var list<string>
     */
    private const TEXT_FIELDS = [
        'rental_name',
        'description',
        'address',
        'whatsapp',
        'instagram',
        'opening_hours',
        'hero_title',
        'hero_subtitle',
        'payment_information',
    ];

    /**
     * @var array<string, string>
     */
    private const DEFAULTS = [
        'rental_name' => 'Nexus Play',
        'description' => '',
        'address' => '',
        'whatsapp' => '',
        'instagram' => '',
        'opening_hours' => '',
        'hero_title' => 'PlayStation Tanpa Batas',
        'hero_subtitle' => 'Nikmati pengalaman bermain terbaik bersama teman-teman.',
        'payment_information' => '',
        'logo' => '',
        'banner' => '',
    ];

    public function __construct(private readonly SettingService $settingsService) {}

    /**
     * Display the site settings form.
     */
    public function edit(): View
    {
        $stored = Setting::query()
            ->whereIn('key', array_keys(self::DEFAULTS))
            ->pluck('value', 'key')
            ->all();

        $settings = self::DEFAULTS;
        foreach ($stored as $key => $value) {
            $settings[$key] = $value ?? '';
        }

        return view('admin.settings', [
            'settings' => $settings,
            'logoUrl' => $this->publicUrl($settings['logo']),
            'bannerUrl' => $this->publicUrl($settings['banner']),
        ]);
    }

    /**
     * Alias for applications that expose settings through an index route.
     */
    public function index(): View
    {
        return $this->edit();
    }

    /**
     * Validate and persist the public-facing rental settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rental_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'address' => ['nullable', 'string', 'max:1000'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'opening_hours' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^(?:[01]\\d|2[0-3]):[0-5]\\d-(?:[01]\\d|2[0-3]):[0-5]\\d$/',
            ],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:1000'],
            'payment_information' => ['nullable', 'string', 'max:5000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        foreach (self::TEXT_FIELDS as $field) {
            if (! array_key_exists($field, $validated)) {
                continue;
            }

            Setting::query()->updateOrCreate(
                ['key' => $field],
                [
                    'value' => $validated[$field] === null ? '' : (string) $validated[$field],
                    'type' => 'string',
                    'group' => 'general',
                ]
            );
        }

        foreach (['logo', 'banner'] as $imageField) {
            if (! $request->hasFile($imageField)) {
                continue;
            }

            $oldPath = Setting::query()->where('key', $imageField)->value('value');
            $newPath = $request->file($imageField)->store('settings', 'public');

            if ($newPath === false) {
                return back()->withInput()->with('error', 'Aset branding gagal disimpan. Silakan coba lagi.');
            }

            Setting::query()->updateOrCreate(
                ['key' => $imageField],
                [
                    'value' => $newPath,
                    'type' => 'image',
                    'group' => 'branding',
                ]
            );

            if ($oldPath && $oldPath !== $newPath) {
                $this->deleteStoredFile($oldPath);
            }
        }

        $this->settingsService->flush();

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    private function publicUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:') || str_starts_with($path, '/')) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    private function deleteStoredFile(string $path): void
    {
        $disk = Storage::disk('public');
        $storagePrefix = trim((string) config('filesystems.disks.public.url'), '/');

        if ($storagePrefix !== '' && str_starts_with($path, $storagePrefix.'/')) {
            $path = substr($path, strlen($storagePrefix) + 1);
        }

        $marker = '/storage/';
        if (str_contains($path, $marker)) {
            $path = substr($path, strpos($path, $marker) + strlen($marker));
        }

        // Only delete paths managed by this application. This prevents an
        // externally supplied URL from being treated as a local file.
        if ($path === '' || str_contains($path, '://') || str_contains($path, '..')) {
            return;
        }

        $disk->delete($path);
    }
}
