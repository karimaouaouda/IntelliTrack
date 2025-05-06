<?php

namespace App\Traits;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Attendable
{
    public function attendances(): MorphMany
    {
        return $this->morphMany(Attendance::class, 'attendable');
    }

    public function recordAttendance(string $type = 'in', ?string $deviceId = null): Attendance
    {
        return $this->attendances()->create([
            'type' => $type,
            'recorded_at' => now(),
            'device_id' => $deviceId,
        ]);
    }

    public function getLatestAttendance(): ?Attendance
    {
        return $this->attendances()->latest()->first();
    }

    public function isPresent(): bool
    {
        $latest = $this->getLatestAttendance();
        return $latest && $latest->type === 'in';
    }
} 