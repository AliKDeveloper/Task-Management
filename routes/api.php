<?php

use App\Http\Controllers\TestController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\SubtaskController;
use App\Http\Controllers\V1\TaskController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user-info', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']);

// Routes for auth
Route::middleware('auth:sanctum')->group(function (){
    // Routes for Tests Purposes
    Route::get('test', [TestController::class, 'test']);

    // Routes for tasks
    Route::get('tasks', [TaskController::class, 'index']);
    Route::get('tasks/{task}', [TaskController::class, 'show']);
    Route::post('tasks/{task}/change-status', [TaskController::class, 'changeStatus']);

    // Routes for users
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{user}', [UserController::class, 'show']);

    Route::middleware('product-owner')->group(function () {
        // Routes where only product owner can access

        Route::post('register', [AuthController::class, 'register']);

        // Routes for Tasks
        Route::post('tasks', [TaskController::class, 'store']);
        Route::put('tasks/{task}', [TaskController::class, 'update']);
        Route::delete('tasks/{task}', [TaskController::class, 'destroy']);
        Route::get('tasks/po-review', [TaskController::class, 'PoReviewTasks']);
        Route::post('tasks/{task}/assign/{user}', [TaskController::class, 'assignTask']);

        // Routes for subtasks
        Route::get('tasks/{task}/subtasks', [SubtaskController::class, 'index']);
        Route::get('tasks/{task}/subtasks/{subtask}', [SubtaskController::class, 'show']);
        Route::post('tasks/{task}/subtasks', [SubtaskController::class, 'store']);
        Route::put('subtasks/{subtask}', [SubtaskController::class, 'update']);
        Route::delete('subtasks/{subtask}', [SubtaskController::class, 'destroy']);

        // Routes for Users
        Route::post('users', [UserController::class, 'store']);
        Route::put('users/{user}', [UserController::class, 'update']);
        Route::delete('users/{user}', [UserController::class, 'destroy']);
   });

});
