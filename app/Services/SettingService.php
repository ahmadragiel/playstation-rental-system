<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingService
{
    private const CACHE_KEY = 'rental.settings.all';

    /** @return array<string, mixed> */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHours(2), function (): array {
            if (! Schema::hasTable('settings')) {
                return [];
            }

            return Setting::query()
                ->orderBy('id')
                ->pluck('value', 'key')
                ->all();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    /** @param array<string, mixed> $values */
    public function putMany(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_array($value) ? json_encode($value, JSON_THROW_ON_ERROR) : (string) $value,
                    'type' => is_array($value) ? 'json' : 'string',
                    'group' => $group,
                ],
            );
        }

        Cache::forget(self::CACHE_KEY);
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
