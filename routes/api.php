<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('task', TaskController::class);

Route::put('task/{id}/toggle-completion', [TaskController::class, 'taskCompleted']);

Route::get('/reminder', [TaskController::class, 'reminderTask']);

Route::get('/check-notification/{id}', [TaskController::class, 'checkNotification']);
