<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $students = Student::all();
        $teachers = User::role('teacher')->get();
        $administrators = User::role('administrator')->get();

        // Generate attendance for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Generate student attendance
        foreach ($students as $student) {
            $this->generateAttendanceForUser(
                $student,
                Student::class,
                $startDate,
                $endDate,
                $faker,
                [
                    'attendance_rate' => 0.95, // 95% attendance rate
                    'check_in_start' => 7, // 7:00 AM
                    'check_in_end' => 9, // 9:00 AM
                    'check_out_start' => 14, // 2:00 PM
                    'check_out_end' => 16, // 4:00 PM
                    'check_out_rate' => 0.90, // 90% chance of check-out
                ]
            );
        }

        // Generate teacher attendance
        foreach ($teachers as $teacher) {
            $this->generateAttendanceForUser(
                $teacher,
                User::class,
                $startDate,
                $endDate,
                $faker,
                [
                    'attendance_rate' => 0.98, // 98% attendance rate
                    'check_in_start' => 6, // 6:00 AM
                    'check_in_end' => 8, // 8:00 AM
                    'check_out_start' => 15, // 3:00 PM
                    'check_out_end' => 17, // 5:00 PM
                    'check_out_rate' => 0.95, // 95% chance of check-out
                ]
            );
        }

        // Generate administrator attendance
        foreach ($administrators as $administrator) {
            $this->generateAttendanceForUser(
                $administrator,
                User::class,
                $startDate,
                $endDate,
                $faker,
                [
                    'attendance_rate' => 0.99, // 99% attendance rate
                    'check_in_start' => 7, // 7:00 AM
                    'check_in_end' => 9, // 9:00 AM
                    'check_out_start' => 16, // 4:00 PM
                    'check_out_end' => 18, // 6:00 PM
                    'check_out_rate' => 0.98, // 98% chance of check-out
                ]
            );
        }
    }

    protected function generateAttendanceForUser(
        $user,
        string $userType,
        Carbon $startDate,
        Carbon $endDate,
        $faker,
        array $config
    ): void {
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            // Skip weekends for students and teachers, but not for administrators
            if ($userType === Student::class || $userType === User::class && $user->hasRole('teacher')) {
                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }
            }

            // Check attendance rate
            if ($faker->boolean($config['attendance_rate'] * 100)) {
                // Generate check-in time
                $checkIn = $currentDate->copy()
                    ->setHour($faker->numberBetween($config['check_in_start'], $config['check_in_end']))
                    ->setMinute($faker->numberBetween(0, 59));

                // Create check-in record
                Attendance::create([
                    'attendable_type' => $userType,
                    'attendable_id' => $user->id,
                    'type' => 'in',
                    'device_id' => 'DEV-' . strtoupper($faker->bothify('??###')),
                    'recorded_at' => $checkIn,
                ]);

                // Check for check-out time
                if ($faker->boolean($config['check_out_rate'] * 100)) {
                    $checkOut = $currentDate->copy()
                        ->setHour($faker->numberBetween($config['check_out_start'], $config['check_out_end']))
                        ->setMinute($faker->numberBetween(0, 59));

                    // Create check-out record
                    Attendance::create([
                        'attendable_type' => $userType,
                        'attendable_id' => $user->id,
                        'type' => 'out',
                        'device_id' => 'DEV-' . strtoupper($faker->bothify('??###')),
                        'recorded_at' => $checkOut,
                    ]);
                }
            }

            $currentDate->addDay();
        }
    }
} 