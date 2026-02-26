<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    // Listar tarefas - agora aceita ?date=YYYY-MM-DD
    public function index(Request $request)
    {
        $query = Task::query();
        if ($request->has('date')) {
            $query->whereDate('start_time', $request->date);
        }
        return $query->orderBy('start_time', 'asc')->get();
    }

    // Criar nova tarefa
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

    // Busca notificações próximas (1h antes)
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

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->update($request->all());
        return response()->json($task);
    }

    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return response()->json(['message' => 'Removida']);
    }
}