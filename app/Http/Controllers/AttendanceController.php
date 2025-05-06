<?php

namespace App\Http\Controllers;

use App\Events\AttendanceRecorded;
use App\Models\Student;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function recordAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'ref_id' => 'required|string',
            'device_id' => 'required|string',
        ]);

        // Try to find the user first
        $user = User::where('ref_id', $request->ref_id)->first();
        if ($user) {
            return $this->processAttendance($user, $request->device_id);
        }

        // If not found as user, try to find as student
        $student = Student::where('ref_id', $request->ref_id)->first();
        if ($student) {
            return $this->processAttendance($student, $request->device_id);
        }

        return response()->json([
            'message' => 'User or Student not found',
        ], 404);
    }

    private function processAttendance(User|Student $attendable, string $deviceId): JsonResponse
    {
        // Determine if this is an "in" or "out" attendance
        $latestAttendance = $attendable->getLatestAttendance();
        $type = (!$latestAttendance || $latestAttendance->type === 'out') ? 'in' : 'out';

        // Record the attendance
        $attendance = $attendable->recordAttendance($type, $deviceId);

        // Dispatch the event
        event(new AttendanceRecorded($attendance, $deviceId));

        return response()->json([
            'message' => 'Attendance recorded successfully',
            'attendable' => [
                'id' => $attendable->id,
                'name' => $attendable->name,
                'type' => $attendable instanceof User ? 'user' : 'student',
                'role' => $attendable instanceof User ? $attendable->roles()->first()?->name : 'student',
            ],
            'attendance' => [
                'type' => $type,
                'recorded_at' => $attendance->recorded_at,
            ],
        ]);
    }

    public function getUserAttendance(User $user): JsonResponse
    {
        if (!Auth::user()->can('view', $user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $attendance = Attendance::where('attendable_type', User::class)
            ->where('attendable_id', $user->id)
            ->orderBy('recorded_at', 'desc')
            ->get();

        return response()->json([
            'data' => $attendance,
            'message' => 'Attendance records retrieved successfully'
        ]);
    }
}
