<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;

// Redireciona a raiz para o dashboard (agenda)
Route::get('/', function () {
    Route::get('/dashboard', [TaskController.php, 'index'])->name('dashboard');
});

// Suas rotas protegidas
Route::middleware(['auth'])->group(function () {
    
    // Ajustado para 'dashboard' para compatibilidade com o Laravel Breeze
    Route::get('/agenda', function () {
        return view('welcome');
    })->name('dashboard'); 

    // Rotas de Perfil (necessárias para o funcionamento do menu do Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas de Tarefas
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});

// Importa as rotas de login/registro (auth.php)
require __DIR__.'/auth.php';