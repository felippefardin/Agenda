<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', Auth::id())
            ->orderBy('start_time', 'asc')
            ->get();

        return view('dashboard', compact('tasks')); 
    }

    public function store(Request $request)
    {
        // MOSTRA TUDO QUE O FORMULÁRIO ESTÁ ENVIANDO
        dd($request->all());

        // Validação
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            'priority' => 'required|in:urgent,important,optional',
            'status' => 'required|string', 
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time'
        ]);

        // adiciona usuário
        $validated['user_id'] = Auth::id();

        // cria tarefa
        Task::create($validated);

        return redirect()->route('dashboard')->with('success', 'Tarefa criada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            'priority' => 'required|in:urgent,important,optional',
            'status' => 'required|string', 
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time'
        ]);

        $task->update($validated);
        
        return response()->json($task);
    }

    public function destroy($id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        $task->delete();
        
        return response()->json(['message' => 'Removida']);
    }
}