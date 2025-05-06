<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classroom;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $classrooms = Classroom::all();

        // Define subject areas and their corresponding grade levels
        $subjectAreas = [
            'Elementary' => [
                'General Education',
                'Physical Education',
                'Art',
                'Music',
            ],
            'Middle School' => [
                'Mathematics',
                'Science',
                'English',
                'History',
                'Physical Education',
                'Art',
                'Music',
                'Computer Science',
            ],
            'High School' => [
                'Mathematics',
                'Physics',
                'Chemistry',
                'Biology',
                'English Literature',
                'World History',
                'Geography',
                'Physical Education',
                'Art',
                'Music',
                'Computer Science',
                'Economics',
                'Psychology',
            ],
        ];

        // Create teachers for each subject area
        foreach ($subjectAreas as $level => $subjects) {
            foreach ($subjects as $subject) {
                // Create 1-3 teachers per subject
                $teacherCount = $faker->numberBetween(1, 3);
                for ($i = 0; $i < $teacherCount; $i++) {
                    $teacher = User::create([
                        'name' => $faker->name,
                        'email' => $faker->unique()->safeEmail,
                        'password' => bcrypt('password'),
                        'ref_id' => 'TCH-' . strtoupper($faker->bothify('??###')),
                    ]);

                    // Assign teacher role
                    $teacher->assignRole('teacher');

                    // Assign additional roles based on experience


                    // Assign to appropriate classrooms
                    $eligibleClassrooms = $classrooms;

                    // Assign to 2-4 classrooms
                    $assignedClassrooms = $eligibleClassrooms->random($faker->numberBetween(2, 4));
                    $teacher->classrooms()->attach($assignedClassrooms->pluck('id')->toArray());

                    // Create teacher profile
                    $teacher->profile()->create([
                        'subject' => $subject,
                        'qualification' => $faker->randomElement([
                            'Bachelor of Education',
                            'Master of Education',
                            'PhD in Education',
                        ]),
                        'experience_years' => $faker->numberBetween(1, 20),
                        'specialization' => $faker->optional(0.7)->randomElement([
                            'Special Education',
                            'Gifted Education',
                            'ESL',
                            'STEM',
                            'Arts Integration',
                        ]),
                        'bio' => $faker->optional(0.8)->paragraph,
                        'phone' => $faker->phoneNumber,
                        'address' => $faker->address,
                        'emergency_contact' => $faker->phoneNumber,
                    ]);
                }
            }
        }

        // Create special education teachers
        for ($i = 0; $i < 3; $i++) {
            $teacher = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'ref_id' => 'TCH-' . strtoupper($faker->bothify('??###')),
            ]);

            $teacher->assignRole('teacher');
            $teacher->assignRole('special_education');

            // Assign to random classrooms across all levels
            $assignedClassrooms = $classrooms->random($faker->numberBetween(3, 5));
            $teacher->classrooms()->attach($assignedClassrooms->pluck('id')->toArray());

            // Create teacher profile
            $teacher->profile()->create([
                'subject' => 'Special Education',
                'qualification' => 'Master of Special Education',
                'experience_years' => $faker->numberBetween(3, 15),
                'specialization' => $faker->randomElement([
                    'Learning Disabilities',
                    'Autism Spectrum',
                    'Behavioral Disorders',
                    'Physical Disabilities',
                ]),
                'bio' => $faker->optional(0.8)->paragraph,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'emergency_contact' => $faker->phoneNumber,
            ]);
        }
    }
}
