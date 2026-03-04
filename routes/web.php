<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    return view('welcome');
});

// Rotas de Tarefas
Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::put('/tasks/{id}', [TaskController::class, 'update']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);

// Rotas do Gerenciador de Arquivos
Route::get('/files', [FileController::class, 'index']);
Route::post('/files', [FileController::class, 'store']);
Route::delete('/files-delete', [FileController::class, 'destroy']);