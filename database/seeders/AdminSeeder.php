<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'karimaouaouda.officiel@gmail.com';
        $password = 'cpplang24';
        $name = 'karim aouaouda';

        $user = new User(compact('name', 'email', 'password'));

        $user->save();
    }
}
