<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceStatisticsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    public function __construct(
        private readonly AttendanceStatisticsService $statisticsService
    ) {}

    public function getUserReport(Request $request, $id): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $attendable_type = $request->input('type', 'user') == 'user' ? 'user' : 'student';

        $user = $attendable_type == 'user' ? User::query()->findOrFail($id) : Student::query()->findOrFail($id);

        $stats = $this->statisticsService->getUserAttendanceStats($user, $startDate, $endDate);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $attendable_type == 'user' ? $user->roles->first()?->name : 'student'
            ],
            'statistics' => $stats,
        ]);
    }

    public function getClassroomReport(Request $request, Classroom $classroom): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $stats = $this->statisticsService->getClassroomAttendanceStats($classroom->id, $startDate, $endDate);

        return response()->json([
            'classroom' => [
                'id' => $classroom->id,
                'name' => $classroom->name,
            ],
            'statistics' => $stats,
        ]);
    }

    public function exportClassroomReport(Request $request, Classroom $classroom): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $stats = $this->statisticsService->getClassroomAttendanceStats($classroom->id, $startDate, $endDate);

        // Here you can implement the export logic (CSV, Excel, PDF, etc.)
        // For now, we'll just return the data
        return response()->json([
            'classroom' => [
                'id' => $classroom->id,
                'name' => $classroom->name,
            ],
            'statistics' => $stats,
            'export_format' => $request->input('format', 'json'),
        ]);
    }
}
