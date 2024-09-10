<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubtaskStoreRequest;
use App\Http\Requests\SubtaskUpdateRequest;
use App\Http\Resources\SubtaskResource;
use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    public function index(Task $task)
    {
        return SubtaskResource::collection($task->subtasks);
    }

    public function show(Task $task, Subtask $subtask)
    {
        $data = $task->subtasks()->find($subtask->id);

        if (!$data)
        {
            abort(404);
        }
        return new SubtaskResource($data);
    }

    public function store(SubtaskStoreRequest $request, Task $task)
    {
        $data = $request->validated();
        $task->subtasks()->create($data);

        return response()->json(['message' => 'Subtask created successfully'], 201);
    }


    public function update(SubtaskUpdateRequest $request, Subtask $subtask)
    {
        $data = $request->validated();
        $subtask->update($data);

        return response()->json(['message' => 'Subtask updated successfully'], 200);
    }


    public function destroy(Subtask $subtask)
    {
        $subtask->delete();

        return response()->json(['message' => 'Subtask deleted successfully'], 200);
    }

}
