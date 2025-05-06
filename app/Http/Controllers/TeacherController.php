<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function getClassrooms(User $teacher): JsonResponse
    {
        // Check if user is authorized
//        if (!Auth::user()->can('view', $teacher)) {
//            return response()->json(['message' => 'Unauthorized'], 403);
//        }

        // Check if user is a teacher
        if (!$teacher->hasRole('teacher')) {
            return response()->json(['message' => 'User is not a teacher'], 400);
        }

        $classrooms = $teacher->classrooms()
            ->with(['students', 'schedules'])
            ->get();

        return response()->json([
            'data' => $classrooms,
            'message' => 'Classrooms retrieved successfully'
        ]);
    }

    public function getSchedules(User $teacher): JsonResponse
    {
//        if (!Auth::user()->can('view', $teacher)) {
//            return response()->json(['message' => 'Unauthorized'], 403);
//        }

//        if (!$teacher->hasRole('teacher')) {
//            return response()->json(['message' => 'User is not a teacher'], 400);
//        }

        $schedules = $teacher->schedules()
            ->with(['classroom'])
            ->get();

        return response()->json([
            'data' => $schedules,
            'message' => 'Schedules retrieved successfully'
        ]);
    }

    public function getAttendance(User $teacher): JsonResponse
    {
        if (!Auth::user()->can('view', $teacher)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$teacher->hasRole('teacher')) {
            return response()->json(['message' => 'User is not a teacher'], 400);
        }

        $attendance = Attendance::where('attendable_type', User::class)
            ->where('attendable_id', $teacher->id)
            ->orderBy('recorded_at', 'desc')
            ->get();

        return response()->json([
            'data' => $attendance,
            'message' => 'Attendance records retrieved successfully'
        ]);
    }
}
