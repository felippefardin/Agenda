<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/tasks', [TaskController::class, 'index']);
Route::get('/tasks-weekly', [TaskController::class, 'getWeekly']);
Route::get('/tasks/{id}', [TaskController::class, 'show']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::put('/tasks/{id}', [TaskController::class, 'update']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);