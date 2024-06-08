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
    Route::get('users-product-owners', [UserController::class, 'getProductOwners']);
    Route::get('users-developers', [UserController::class, 'getDevelopers']);
    Route::get('users-testers', [UserController::class, 'getTesters']);

    // Task routes
    Route::resource('tasks', TaskController::class);
    Route::post('tasks-import', [TaskController::class, 'import']);
    Route::get('tasks-export', [TaskController::class, 'export']);
    Route::get('tasks-progress', [TaskController::class, 'progress']);

    // JobProgress
    Route::get('import-progress/{batchId}', [JobProgressController::class, 'getImportProgress']);

    // Subtask routes
    Route::prefix('tasks/{taskId}')->group(function () {
        Route::get('subtasks', [SubtaskController::class, 'index']);
        Route::get('subtasks/{subtaskId}', [SubtaskController::class, 'show']);
        Route::post('subtasks', [SubtaskController::class, 'store']);
        Route::put('subtasks/{subtaskId}', [SubtaskController::class, 'update']);
        Route::delete('subtasks/{subtaskId}', [SubtaskController::class, 'destroy']);
    });

    // Change Task Status
    Route::put('tasks/{taskId}/status/{status}', [TaskController::class, 'changeStatus']);


    // Get Po Review Tasks
    Route::get('tasks-po-review', [TaskController::class, 'getPoReviewTasks']);

    // get user role
    Route::get('user-role', function (){
        return response()->json(auth()->user()->role);
    });
});
