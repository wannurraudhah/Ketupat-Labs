<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->role === 'teacher') {
            $query = Classroom::query()
                ->where('teacher_id', $user->id)
                ->orderByDesc('created_at');
        } else {
            $query = Classroom::query()
                ->whereHas('students', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->orderByDesc('created_at');
        }

        return response()->json($query->get());
    }

    public function show(Classroom $class)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $this->authorize('view', $class);

        return response()->json($class);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $this->authorize('create', Classroom::class);

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:200'],
                'subject' => ['required', 'string', 'max:200'],
                'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            ], [
                'name.required' => 'The class name is required.',
                'name.max' => 'The class name may not be greater than 200 characters.',
                'subject.required' => 'The subject is required.',
                'subject.max' => 'The subject may not be greater than 200 characters.',
                'year.integer' => 'The year must be a valid number.',
                'year.min' => 'The year must be at least 2000.',
                'year.max' => 'The year may not be greater than 2100.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $classroom = Classroom::create([
            'teacher_id' => $user->id,
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'year' => $validated['year'] ?? null,
        ]);

        return response()->json($classroom, Response::HTTP_CREATED);
    }

    public function update(Request $request, Classroom $class)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $this->authorize('update', $class);

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:200'],
                'subject' => ['required', 'string', 'max:200'],
                'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            ], [
                'name.required' => 'The class name is required.',
                'name.max' => 'The class name may not be greater than 200 characters.',
                'subject.required' => 'The subject is required.',
                'subject.max' => 'The subject may not be greater than 200 characters.',
                'year.integer' => 'The year must be a valid number.',
                'year.min' => 'The year must be at least 2000.',
                'year.max' => 'The year may not be greater than 2100.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $class->update($validated);

        return response()->json($class);
    }

    public function destroy(Classroom $class)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $this->authorize('delete', $class);

        $class->delete();

        return response()->json(['success' => true]);
    }

    public function getStudents(Classroom $class)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $this->authorize('view', $class);

        $students = $class->students()->get();

        return response()->json($students);
    }

    public function addStudent(Request $request, Classroom $class)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $this->authorize('update', $class);

        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
        ], [
            'student_id.required' => 'Student ID is required.',
            'student_id.exists' => 'The selected student does not exist.',
        ]);

        // Check if student is already enrolled
        if ($class->students()->where('users.id', $validated['student_id'])->exists()) {
            return response()->json(['message' => 'Student is already enrolled in this class'], Response::HTTP_CONFLICT);
        }

        // Check if the user is actually a student
        $student = \App\Models\User::find($validated['student_id']);
        if ($student->role !== 'student') {
            return response()->json(['message' => 'Only students can be added to classes'], Response::HTTP_BAD_REQUEST);
        }

        $class->students()->attach($validated['student_id'], ['enrolled_at' => now()]);

        $student = $class->students()->where('users.id', $validated['student_id'])->first();

        return response()->json($student, Response::HTTP_CREATED);
    }

    public function removeStudent(Classroom $class, $studentId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $this->authorize('update', $class);

        $class->students()->detach($studentId);

        return response()->json(['success' => true]);
    }
}


