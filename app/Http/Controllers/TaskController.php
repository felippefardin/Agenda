<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::where('user_id', Auth::id()); 
        
        if ($request->has('date')) {
            $query->whereDate('start_time', $request->date);
        }
        
        return $query->orderBy('start_time', 'asc')->get();
    }

    public function store(Request $request)
    {
        // 1. Validação estrita
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            'priority' => 'required|in:urgent,important,optional',
            'status' => 'required|string', 
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time'
        ]);

        // 2. Adiciona o user_id apenas aos dados validados
        $validated['user_id'] = Auth::id();

        // 3. Cria usando apenas o que passou na validação + user_id
        $task = Task::create($validated);
        
        return response()->json($task, 201);
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