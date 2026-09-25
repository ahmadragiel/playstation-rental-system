<?php

namespace App\Models;

use App\Enums\ScheduleStatus;
use App\Enums\UnitStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class PlaystationUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'playstation_type_id',
        'code',
        'name',
        'condition',
        'status',
        'location',
        'notes',
        'photo',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'status' => UnitStatus::class,
            'is_active' => 'boolean',
        ];
    }

    public function getPhotoUrlAttribute(): ?string
    {
        $path = trim((string) $this->photo);

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(PlaystationType::class, 'playstation_type_id');
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_playstation_unit');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeBookable(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('status', '!=', UnitStatus::Maintenance->value);
    }

    public function hasConflict(Carbon|string $startAt, Carbon|string $endAt, ?int $ignoreBookingId = null): bool
    {
        $query = $this->schedules()
            ->whereIn('status', [ScheduleStatus::Reserved->value, ScheduleStatus::InProgress->value])
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt);

        if ($ignoreBookingId) {
            $query->where('booking_id', '!=', $ignoreBookingId);
        }

        return $query->exists();
    }

    public function effectiveStatus(?CarbonInterface $at = null): UnitStatus
    {
        if ($this->status === UnitStatus::Maintenance || ! $this->is_active) {
            return $this->status === UnitStatus::Maintenance
                ? UnitStatus::Maintenance
                : UnitStatus::Maintenance;
        }

        $at ??= now();
        $activeStatuses = [ScheduleStatus::Reserved->value, ScheduleStatus::InProgress->value];
        $schedules = $this->relationLoaded('schedules')
            ? $this->schedules
            : $this->schedules()->get();

        $schedule = $schedules
            ->filter(fn ($schedule): bool => in_array(
                $schedule->status instanceof \BackedEnum ? $schedule->status->value : $schedule->status,
                $activeStatuses,
                true,
            )
                && $schedule->start_at->lessThanOrEqualTo($at)
                && $schedule->end_at->greaterThan($at))
            ->sortBy(fn ($schedule): array => [
                $schedule->status === ScheduleStatus::InProgress ? 0 : 1,
                -$schedule->start_at->getTimestamp(),
            ])
            ->first();

        if ($schedule?->status === ScheduleStatus::InProgress) {
            return UnitStatus::InUse;
        }

        if ($schedule) {
            return UnitStatus::Booked;
        }

        return $this->status === UnitStatus::Available ? UnitStatus::Available : UnitStatus::Available;
    }
}
