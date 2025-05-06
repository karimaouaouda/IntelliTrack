<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Get all classrooms and teachers
        $classrooms = Classroom::all();
        $teachers = User::role('teacher')->get();

        if ($classrooms->isEmpty() || $teachers->isEmpty()) {
            $this->command->error('No classrooms or teachers found. Please run ClassroomSeeder and UserSeeder first.');
            return;
        }

        // Sample subjects
        $subjects = [
            'Mathematics',
            'Physics',
            'Chemistry',
            'Biology',
            'English',
            'History',
            'Geography',
            'Computer Science',
            'Physical Education',
            'Art'
        ];

        // Days of the week
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        // Time slots (in 24-hour format)
        $timeSlots = [
            ['start' => '08:00', 'end' => '09:30'],
            ['start' => '09:45', 'end' => '11:15'],
            ['start' => '11:30', 'end' => '13:00'],
            ['start' => '14:00', 'end' => '15:30'],
            ['start' => '15:45', 'end' => '17:15']
        ];

        // Create schedules for each classroom
        foreach ($classrooms as $classroom) {
            // Assign 5-7 subjects per classroom
            $classroomSubjects = array_rand(array_flip($subjects), rand(5, 7));

            foreach ($days as $day) {
                // Assign 3-5 time slots per day
                $dayTimeSlots = array_rand($timeSlots, rand(3, 5));

                if (!is_array($dayTimeSlots)) {
                    $dayTimeSlots = [$dayTimeSlots];
                }

                foreach ($dayTimeSlots as $timeSlotIndex) {
                    $timeSlot = $timeSlots[$timeSlotIndex];

                    // Randomly select a teacher
                    $teacher = $teachers->random();

                    // Randomly select a subject for this time slot
                    $subject = is_array($classroomSubjects)
                        ? $classroomSubjects[array_rand($classroomSubjects)]
                        : $classroomSubjects;

                    Schedule::create([
                        'classroom_id' => $classroom->id,
                        'teacher_id' => $teacher->id,
                        //'subject' => $subject,
                        'day_of_week' => $day,
                        'start_time' => $timeSlot['start'],
                        'end_time' => $timeSlot['end'],
                    ]);
                }
            }
        }

        $this->command->info('Schedules seeded successfully!');
    }
}
