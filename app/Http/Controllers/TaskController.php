<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // 🔹 Listar todas as tarefas
    public function index()
    {
        return Task::orderBy('start_time', 'asc')->get();
    }

    // 🔹 Listar tarefas da semana atual
    public function getWeekly()
    {
        return Task::whereBetween('start_time', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->orderBy('start_time', 'asc')
            ->get();
    }

    // 🔹 Criar nova tarefa
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|in:Work,Short-term Project,Long-term Project',
            'priority' => 'required|in:urgent,important,optional',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        $task = Task::create($request->all());

        return response()->json($task, 201);
    }

    // 🔹 Mostrar tarefa específica
    public function show($id)
    {
        return Task::findOrFail($id);
    }

    // 🔹 Atualizar tarefa
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|in:Work,Short-term Project,Long-term Project',
            'priority' => 'sometimes|required|in:urgent,important,optional',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time'
        ]);

        $task->update($request->all());

        return response()->json($task);
    }

    // 🔹 Deletar tarefa
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Tarefa removida com sucesso']);
    }
}