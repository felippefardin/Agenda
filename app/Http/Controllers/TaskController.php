<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();
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

        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
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
        Task::findOrFail($id)->delete();
        return response()->json(['message' => 'Removida']);
    }
}