<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classroom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $teacher = User::query()->updateOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Alice Teacher',
                'full_name' => 'Alice Teacher',
                'username' => 'alice_teacher',
                'password' => Hash::make('secret123'),
                'role' => 'teacher',
            ],
        );

        $student = User::query()->updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Bob Student',
                'full_name' => 'Bob Student',
                'username' => 'bob_student',
                'password' => Hash::make('secret123'),
                'role' => 'student',
            ],
        );

        // Create classrooms
        $math = Classroom::query()->updateOrCreate(
            ['name' => 'Math 101', 'teacher_id' => $teacher->id],
            [
                'subject' => 'Mathematics',
                'year' => 2025,
            ],
        );

        $science = Classroom::query()->updateOrCreate(
            ['name' => 'Science Explorations', 'teacher_id' => $teacher->id],
            [
                'subject' => 'Science',
                'year' => 2025,
            ],
        );

        // Enroll student in classrooms
        try {
            $math->students()->syncWithoutDetaching([
                $student->id => ['enrolled_at' => now()],
            ]);

            $science->students()->syncWithoutDetaching([
                $student->id => ['enrolled_at' => now()],
            ]);
        } catch (\Exception $e) {
            // If relationship fails, at least users and classrooms are created
            \Log::warning('Failed to enroll students in classrooms: ' . $e->getMessage());
        }
    }
}
