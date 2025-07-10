<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PersonController;

Route::prefix('tasks')->group(function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::post('/', [TaskController::class, 'store']);
    Route::put('/{task}', [TaskController::class, 'update']);
    Route::delete('/{task}', [TaskController::class, 'destroy']);
    Route::post('/{task}/assign', [TaskController::class, 'assign_task']);
    Route::post('/{task}/unassign', [TaskController::class, 'unassign_task']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']); 
});

// Rutas que no se solicita pero la considero necesaria
Route::prefix('persons')->group(function () {
    Route::get('/', [PersonController::class, 'index']);
    Route::post('/', [PersonController::class, 'store']);
    Route::delete('/{person}', [PersonController::class, 'destroy']);
});
