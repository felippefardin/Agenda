<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página inicial (padrão do Laravel)
Route::get('/', function () {
    return view('welcome');
});

// 🔹 ROTAS DA AGENDA

// Listar todas as tarefas
Route::get('/tasks', [TaskController::class, 'index']);

// Listar tarefas da semana
Route::get('/tasks-weekly', [TaskController::class, 'getWeekly']);

// Mostrar tarefa específica
Route::get('/tasks/{id}', [TaskController::class, 'show']);

// Criar nova tarefa
Route::post('/tasks', [TaskController::class, 'store']);

// Atualizar tarefa
Route::put('/tasks/{id}', [TaskController::class, 'update']);

// Deletar tarefa
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);