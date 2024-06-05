<?php

use App\Http\Controllers\JobProgressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\UserController;

// Authentication routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::resource('users', UserController::class);

    // Task routes
    Route::resource('tasks', TaskController::class);
    Route::post('taskss/import', [TaskController::class, 'import']);
    Route::get('taskss/export', [TaskController::class, 'export']);
    Route::get('tasks/progress', [TaskController::class, 'progress']);

    // Subtask routes
    Route::resource('subtasks', SubtaskController::class);
    Route::get('import-progress/{batchId}', [JobProgressController::class, 'getImportProgress']);
});
