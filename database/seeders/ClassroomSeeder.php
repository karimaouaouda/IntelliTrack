<?php

namespace Database\Seeders;

use App\Models\Classroom;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create different grade levels
        $gradeLevels = [
            'Kindergarten' => ['A', 'B', 'C'],
            'Elementary' => ['1', '2', '3', '4', '5'],
            'Middle School' => ['6', '7', '8'],
            'High School' => ['9', '10', '11', '12'],
        ];

        foreach ($gradeLevels as $level => $grades) {
            foreach ($grades as $grade) {
                // Create 2-3 sections per grade
                $sections = $faker->numberBetween(2, 3);
                for ($i = 0; $i < $sections; $i++) {
                    $section = chr(65 + $i); // A, B, C, etc.

                    Classroom::create([
                        'name' => "Grade {$grade} - Section {$section}",
                        //'grade_level' => $level,
                        //'grade' => $grade,
                        //'section' => $section,
                        'capacity' => $faker->numberBetween(20, 30),
                        //'room_number' => $faker->unique()->numberBetween(100, 999),
                        'description' => $faker->optional(0.7)->sentence,
                        //'is_active' => true,
                        //'academic_year' => date('Y') . '-' . (date('Y') + 1),
                    ]);
                }
            }
        }

        // Create special classrooms
        $specialClassrooms = [
            'Computer Lab',
            'Science Lab',
            'Art Room',
            'Music Room',
            'Library',
            'Gymnasium',
        ];

        foreach ($specialClassrooms as $room) {
            Classroom::create([
                'name' => $room,
                //'grade_level' => 'Special',
                //'grade' => 'N/A',
                //'section' => 'N/A',
                'capacity' => $faker->numberBetween(30, 50),
                //'room_number' => $faker->unique()->numberBetween(100, 999),
                'description' => "Specialized classroom for {$room} activities",
                //'is_active' => true,
                //'academic_year' => date('Y') . '-' . (date('Y') + 1),
            ]);
        }
    }
}
