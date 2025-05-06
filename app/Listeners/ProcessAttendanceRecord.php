<?php

namespace App\Listeners;

use App\Events\AttendanceRecorded;
use Illuminate\Support\Facades\Log;

class ProcessAttendanceRecord
{
    public function handle(AttendanceRecorded $event): void
    {
        $attendance = $event->attendance;
        $user = $attendance->user;
        
        // Log the attendance
        Log::info('Attendance recorded', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_type' => $user->roles->first()?->name,
            'type' => $attendance->type,
            'device_id' => $event->deviceId,
            'recorded_at' => $attendance->recorded_at,
        ]);

        // Here you can add additional processing like:
        // - Sending notifications
        // - Updating statistics
        // - Triggering other events
    }
} 