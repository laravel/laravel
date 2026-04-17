<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdateSchedule extends Model
{
    protected $fillable = [
        'is_enabled',
        'starts_at',
        'ends_at',
        'notes',
        'updated_by',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function current(): ?self
    {
        try {
            return static::query()->latest('id')->first();
        } catch (\Throwable $e) {
            // Fail-open before migration runs.
            return null;
        }
    }

    public function hasEnded(): bool
    {
        if (!$this->is_enabled || !$this->ends_at) {
            return false;
        }

        return now()->greaterThanOrEqualTo($this->ends_at);
    }

    public static function isReadOnlyForUser(?User $user): bool
    {
        if (!$user || (!$user->isSubAdmin() && !$user->isPegawai())) {
            return false;
        }

        $schedule = static::current();
        if (!$schedule || !$schedule->is_enabled || !$schedule->ends_at) {
            return false;
        }

        return $schedule->hasEnded();
    }

    public function formattedStartsAt(): ?string
    {
        return $this->starts_at?->format('d/m/Y H:i');
    }

    public function formattedEndsAt(): ?string
    {
        return $this->ends_at?->format('d/m/Y H:i');
    }
}
