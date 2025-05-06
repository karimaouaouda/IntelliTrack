<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $classrooms = Classroom::all();

        // Create 50 students
        for ($i = 0; $i < 50; $i++) {
            $student = Student::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'date_of_birth' => $faker->dateTimeBetween('-18 years', '-5 years'),
                'gender' => $faker->randomElement(['male', 'female']),
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'emergency_contact' => $faker->phoneNumber,
                'medical_conditions' => $faker->optional(0.2)->randomElements([
                    'Asthma',
                    'Allergies',
                    'Diabetes',
                    'ADHD',
                    'None'
                ], $faker->numberBetween(0, 2)),
                'notes' => $faker->optional(0.3)->sentence,
            ]);

            // Assign to random classrooms (1-3 classrooms per student)
            $student->classrooms()->attach(
                $classrooms->random($faker->numberBetween(1, 3))->pluck('id')->toArray()
            );

            // Create parent accounts and link them
            $parentCount = $faker->numberBetween(1, 2);
            for ($j = 0; $j < $parentCount; $j++) {
                $parent = User::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => bcrypt('password'),
                ]);

                $parent->assignRole('parent');

                // Link parent to student
                $student->parents()->attach($parent->id, [
                    'relationship_type' => $j === 0 ? 'parent' : 'guardian',
                    'is_primary_contact' => $j === 0,
                    'notes' => $faker->optional(0.3)->sentence,
                ]);
            }
        }
    }
}
