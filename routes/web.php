<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PersonController;

Route::get('/', function () {
    return redirect()->route('tasks.index');
});

Route::get('/tasks', [TaskController::class, 'indexView'])->name('tasks.index');
Route::get('/persons', [PersonController::class, 'indexView'])->name('persons.index');