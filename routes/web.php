<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Redireciona a raiz para a agenda
Route::get('/', function () {
    return redirect()->route('agenda');
});

// Suas rotas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/agenda', function () {
        return view('welcome');
    })->name('agenda');

    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
});

// ESTA LINHA É ESSENCIAL: ela importa as rotas de login/registro
require __DIR__.'/auth.php';