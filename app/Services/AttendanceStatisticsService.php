<?php

namespace App\Services;

use App\Interfaces\Attendable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceStatisticsService
{
    public function getUserAttendanceStats(Attendable $user, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = $user->attendances();

        if ($startDate) {
            $query->where('recorded_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('recorded_at', '<=', $endDate);
        }

        $attendances = $query->get();

        $totalDays = $this->calculateTotalDays($startDate, $endDate);
        $presentDays = $this->calculatePresentDays($attendances);

        return [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $totalDays - $presentDays,
            'attendance_rate' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0,
            'attendance_history' => $this->formatAttendanceHistory($attendances),
        ];
    }

    public function getClassroomAttendanceStats(int $classroomId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $classroom = Classroom::findOrFail($classroomId);
        $students = $classroom->students;

        $stats = [];
        foreach ($students as $student) {
            $stats[$student->id] = $this->getUserAttendanceStats($student, $startDate, $endDate);
        }

        return [
            'classroom_name' => $classroom->name,
            'total_students' => $students->count(),
            'student_stats' => $stats,
            'class_average' => $this->calculateClassAverage($stats),
        ];
    }

    private function calculateTotalDays(?Carbon $startDate, ?Carbon $endDate): int
    {
        if (!$startDate) {
            $startDate = Carbon::now()->startOfMonth();
        }
        if (!$endDate) {
            $endDate = Carbon::now();
        }

        return $startDate->diffInDays($endDate) + 1;
    }

    private function calculatePresentDays(Collection $attendances): int
    {
        $presentDays = 0;
        $currentDate = null;
        $hasIn = false;

        foreach ($attendances as $attendance) {
            $date = $attendance->recorded_at->format('Y-m-d');

            if ($date !== $currentDate) {
                if ($currentDate && $hasIn) {
                    $presentDays++;
                }
                $currentDate = $date;
                $hasIn = false;
            }

            if ($attendance->type === 'in') {
                $hasIn = true;
            }
        }

        // Don't forget to count the last day
        if ($currentDate && $hasIn) {
            $presentDays++;
        }

        return $presentDays;
    }

    private function formatAttendanceHistory(Collection $attendances): array
    {
        $history = [];
        foreach ($attendances as $attendance) {
            $date = $attendance->recorded_at->format('Y-m-d');
            if (!isset($history[$date])) {
                $history[$date] = [
                    'date' => $date,
                    'first_in' => null,
                    'last_out' => null,
                ];
            }

            if ($attendance->type === 'in') {
                if (!$history[$date]['first_in'] || $attendance->recorded_at < $history[$date]['first_in']) {
                    $history[$date]['first_in'] = $attendance->recorded_at;
                }
            } else {
                if (!$history[$date]['last_out'] || $attendance->recorded_at > $history[$date]['last_out']) {
                    $history[$date]['last_out'] = $attendance->recorded_at;
                }
            }
        }

        return array_values($history);
    }

    private function calculateClassAverage(array $stats): float
    {
        if (empty($stats)) {
            return 0;
        }

        $totalRate = 0;
        foreach ($stats as $studentStat) {
            $totalRate += $studentStat['attendance_rate'];
        }

        return round($totalRate / count($stats), 2);
    }
}
