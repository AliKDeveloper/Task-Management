<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subtask;
use App\Models\Task;

class SubtaskController extends Controller
{
    // Get all subtasks for a specific task
    public function index($taskId)
    {
        $task = Task::findOrFail($taskId);
        return response()->json($task->subtasks);
    }

    // Get a specific subtask
    public function show($taskId, $subtaskId)
    {
        $subtask = Subtask::where('task_id', $taskId)->findOrFail($subtaskId);
        return response()->json($subtask);
    }

    // Create a new subtask
    public function store(Request $request, $taskId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:TODO,IN PROGRESS,READY FOR TEST,PO REVIEW,DONE,REJECTED',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task = Task::findOrFail($taskId);

        $subtask = $task->subtasks()->create($request->all());

        return response()->json($subtask, 201);
    }

    // Update a subtask
    public function update(Request $request, $taskId, $subtaskId)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|required|string|in:TODO,IN PROGRESS,READY FOR TEST,PO REVIEW,DONE,REJECTED',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
        ]);

        $subtask = Subtask::where('task_id', $taskId)->findOrFail($subtaskId);
        $subtask->update($request->all());

        return response()->json($subtask);
    }

    // Delete a subtask
    public function destroy($taskId, $subtaskId)
    {
        $subtask = Subtask::where('task_id', $taskId)->findOrFail($subtaskId);
        $subtask->delete();

        return response()->json(null, 204);
    }
}
