<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{
    public function view(User $user, Classroom $classroom): bool
    {
        if ($user->role === 'teacher') {
            return $classroom->teacher_id === $user->id;
        }

        return $classroom->students()->where('users.id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->role === 'teacher';
    }

    public function update(User $user, Classroom $classroom): bool
    {
        return $user->role === 'teacher' && $classroom->teacher_id === $user->id;
    }

    public function delete(User $user, Classroom $classroom): bool
    {
        return $user->role === 'teacher' && $classroom->teacher_id === $user->id;
    }
}


