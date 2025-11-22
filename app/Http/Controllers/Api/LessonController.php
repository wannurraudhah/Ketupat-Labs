<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Lesson;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    use AuthorizesRequests;

    public function index(Classroom $classroom)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // Teachers can see all lessons, students can only see published lessons
        $query = $classroom->lessons();

        if ($user->role === 'student') {
            $query->where('is_published', true);
        }

        $lessons = $query->orderBy('created_at', 'desc')->get();

        return response()->json($lessons);
    }

    public function store(Request $request, Classroom $classroom)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // Only teachers can create lessons
        if ($user->role !== 'teacher') {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        // Verify teacher owns the classroom
        if ($classroom->teacher_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        try {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'topic' => ['required', 'string', 'max:255'],
                'duration' => ['nullable', 'integer', 'min:1'],
                'material_path' => ['nullable', 'string', 'max:255'],
                'is_published' => ['sometimes', 'boolean'],
            ], [
                'title.required' => 'The lesson title is required.',
                'topic.required' => 'The lesson topic is required.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $lesson = Lesson::create([
            'title' => $validated['title'],
            'topic' => $validated['topic'],
            'teacher_id' => $user->id,
            'classroom_id' => $classroom->id,
            'duration' => $validated['duration'] ?? null,
            'material_path' => $validated['material_path'] ?? null,
            'is_published' => $validated['is_published'] ?? false,
        ]);

        return response()->json($lesson, Response::HTTP_CREATED);
    }

    public function show(Classroom $classroom, Lesson $lesson)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // Verify lesson belongs to classroom
        if ($lesson->classroom_id !== $classroom->id) {
            return response()->json(['message' => 'Lesson not found in this classroom'], Response::HTTP_NOT_FOUND);
        }

        // Students can only see published lessons
        if ($user->role === 'student' && !$lesson->is_published) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        return response()->json($lesson);
    }

    public function update(Request $request, Classroom $classroom, Lesson $lesson)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // Only teachers can update lessons
        if ($user->role !== 'teacher' || $lesson->teacher_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        // Verify lesson belongs to classroom
        if ($lesson->classroom_id !== $classroom->id) {
            return response()->json(['message' => 'Lesson not found in this classroom'], Response::HTTP_NOT_FOUND);
        }

        try {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'topic' => ['required', 'string', 'max:255'],
                'duration' => ['nullable', 'integer', 'min:1'],
                'material_path' => ['nullable', 'string', 'max:255'],
                'is_published' => ['sometimes', 'boolean'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $lesson->update($validated);

        return response()->json($lesson);
    }

    public function destroy(Classroom $classroom, Lesson $lesson)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // Only teachers can delete lessons
        if ($user->role !== 'teacher' || $lesson->teacher_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        // Verify lesson belongs to classroom
        if ($lesson->classroom_id !== $classroom->id) {
            return response()->json(['message' => 'Lesson not found in this classroom'], Response::HTTP_NOT_FOUND);
        }

        $lesson->delete();

        return response()->json(['success' => true]);
    }
}

