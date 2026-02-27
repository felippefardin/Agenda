<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    // Listar tarefas - aceita filtro por data ?date=YYYY-MM-DD
    public function index(Request $request)
    {
        $query = Task::query();
        if ($request->has('date')) {
            $query->whereDate('start_time', $request->date);
        }
        return $query->orderBy('start_time', 'asc')->get();
    }

    // Criar nova tarefa com validação robusta
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'priority' => 'required|in:urgent,important,optional',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time'
        ]);

        $task = Task::create($request->all());
        
        // Uso correto do helper response() para evitar erros fatais
        return response()->json($task, 201);
    }

    // Busca notificações próximas (próxima 1 hora)
    public function checkNotifications()
    {
        return Task::where('is_notified', false)
            ->whereBetween('start_time', [now(), now()->addHour()])
            ->first();
    }

    // Adiar notificação em 10 minutos
    public function snooze($id)
    {
        $task = Task::findOrFail($id);
        $task->update([
            'start_time' => Carbon::parse($task->start_time)->addMinutes(10),
            'is_notified' => false
        ]);
        return response()->json(['message' => 'Adiado']);
    }

    // Atualizar tarefa existente
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:urgent,important,optional',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time'
        ]);

        $task->update($request->all());
        return response()->json($task);
    }

    // Remover tarefa
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(['message' => 'Removida']);
    }
}