<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function index(): JsonResponse
    {
        if (!Auth::user()->can('viewAny', Schedule::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $schedules = Schedule::with(['classroom', 'teacher', 'subject'])
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'data' => $schedules,
            'message' => 'Schedules retrieved successfully'
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!Auth::user()->can('create', Schedule::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for schedule conflicts
        $conflict = Schedule::where('classroom_id', $request->classroom_id)
            ->where('day', $request->day)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Schedule conflict detected',
                'errors' => ['schedule' => ['There is already a schedule in this time slot']]
            ], 422);
        }

        $schedule = Schedule::create($request->all());

        return response()->json([
            'data' => $schedule->load(['classroom', 'teacher', 'subject']),
            'message' => 'Schedule created successfully'
        ], 201);
    }

    public function show(Schedule $schedule): JsonResponse
    {
        if (!Auth::user()->can('view', $schedule)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'data' => $schedule->load(['classroom', 'teacher', 'subject']),
            'message' => 'Schedule retrieved successfully'
        ]);
    }

    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        if (!Auth::user()->can('update', $schedule)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'classroom_id' => 'sometimes|exists:classrooms,id',
            'teacher_id' => 'sometimes|exists:users,id',
            'subject' => 'sometimes|string|max:255',
            'day' => 'sometimes|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for schedule conflicts if time or day is being updated
        if ($request->has('start_time') || $request->has('end_time') || $request->has('day')) {
            $conflict = Schedule::where('classroom_id', $request->classroom_id ?? $schedule->classroom_id)
                ->where('day', $request->day ?? $schedule->day)
                ->where('id', '!=', $schedule->id)
                ->where(function ($query) use ($request, $schedule) {
                    $query->whereBetween('start_time', [
                        $request->start_time ?? $schedule->start_time,
                        $request->end_time ?? $schedule->end_time
                    ])
                    ->orWhereBetween('end_time', [
                        $request->start_time ?? $schedule->start_time,
                        $request->end_time ?? $schedule->end_time
                    ])
                    ->orWhere(function ($q) use ($request, $schedule) {
                        $q->where('start_time', '<=', $request->start_time ?? $schedule->start_time)
                            ->where('end_time', '>=', $request->end_time ?? $schedule->end_time);
                    });
                })
                ->exists();

            if ($conflict) {
                return response()->json([
                    'message' => 'Schedule conflict detected',
                    'errors' => ['schedule' => ['There is already a schedule in this time slot']]
                ], 422);
            }
        }

        $schedule->update($request->all());

        return response()->json([
            'data' => $schedule->load(['classroom', 'teacher', 'subject']),
            'message' => 'Schedule updated successfully'
        ]);
    }

    public function destroy(Schedule $schedule): JsonResponse
    {
        if (!Auth::user()->can('delete', $schedule)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $schedule->delete();

        return response()->json([
            'message' => 'Schedule deleted successfully'
        ]);
    }

    public function getClassroomSchedules(Classroom $classroom): JsonResponse
    {
        if (!Auth::user()->can('view', $classroom)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $schedules = $classroom->schedules()
            ->with(['teacher', 'subject'])
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'data' => $schedules,
            'message' => 'Classroom schedules retrieved successfully'
        ]);
    }

    public function getTeacherSchedules(User $teacher): JsonResponse
    {
        if (!Auth::user()->can('view', $teacher)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$teacher->hasRole('teacher')) {
            return response()->json(['message' => 'User is not a teacher'], 400);
        }

        $schedules = $teacher->schedules()
            ->with(['classroom', 'subject'])
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'data' => $schedules,
            'message' => 'Teacher schedules retrieved successfully'
        ]);
    }
} 