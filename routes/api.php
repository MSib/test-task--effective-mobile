<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::controller(TaskController::class)->group(function () {
    Route::get('/tasks', 'index');
    Route::post('/tasks', 'store');
    Route::get('/tasks/{id}', 'show');
    Route::put('/tasks/{id}', 'update');
    Route::delete('/tasks/{id}', 'destroy');
});
