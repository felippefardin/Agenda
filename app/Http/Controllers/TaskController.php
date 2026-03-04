<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // Adicionado para facilitar o uso do Auth

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // FILTRO DE SEGURANÇA: Começa a query filtrando apenas tarefas do usuário logado
        $query = Task::where('user_id', Auth::id()); 
        
        if ($request->has('date')) {
            $query->whereDate('start_time', $request->date);
        }
        
        return $query->orderBy('start_time', 'asc')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:urgent,important,optional',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time'
        ]);

        // SEGURANÇA: Adiciona o user_id do usuário logado aos dados salvos
        $data = $request->all();
        $data['user_id'] = Auth::id();

        $task = Task::create($data);
        return response()->json($task, 201);
    }

    public function update(Request $request, $id)
    {
        // SEGURANÇA: findOrFail com filtro de usuário garante que ele não edite tarefas alheias
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:urgent,important,optional',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time'
        ]);

        $task->update($request->all());
        return response()->json($task);
    }

    public function destroy($id)
    {
        // SEGURANÇA: Filtro de usuário para impedir exclusão de tarefas de outros
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        $task->delete();
        
        return response()->json(['message' => 'Removida']);
    }
}