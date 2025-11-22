<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('classes', ClassroomController::class);
    
    // Student management routes
    Route::get('/classes/{class}/students', [ClassroomController::class, 'getStudents']);
    Route::post('/classes/{class}/students', [ClassroomController::class, 'addStudent']);
    Route::delete('/classes/{class}/students/{student}', [ClassroomController::class, 'removeStudent']);
    
    // Get all students (for adding to classes)
    Route::get('/students', [StudentController::class, 'index']);
    
    // Lessons routes (nested under classrooms)
    Route::get('/classes/{classroom}/lessons', [\App\Http\Controllers\Api\LessonController::class, 'index']);
    Route::post('/classes/{classroom}/lessons', [\App\Http\Controllers\Api\LessonController::class, 'store']);
    Route::get('/classes/{classroom}/lessons/{lesson}', [\App\Http\Controllers\Api\LessonController::class, 'show']);
    Route::put('/classes/{classroom}/lessons/{lesson}', [\App\Http\Controllers\Api\LessonController::class, 'update']);
    Route::delete('/classes/{classroom}/lessons/{lesson}', [\App\Http\Controllers\Api\LessonController::class, 'destroy']);
});


