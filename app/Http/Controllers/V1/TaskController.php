<?php

namespace App\Http\Controllers\V1;

use App\Enums\TaskStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskLogResource;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role->id === 1) // Product Owner
        {
            $tasks = Task::where('created_by', auth()->user()->id)->get();
        }

        elseif (auth()->user()->role->id === 2) // Developer
        {
            $tasks = Task::where('developed_by', auth()->user()->id)->get();
        }

        elseif (auth()->user()->role->id === 3) // Tester
        {
            $tasks = Task::where('tested_by', auth()->user()->id)->get();
        }

        else
        {
            return response()->json(['message' => 'You are not authorized to see any tasks'], 403);
        }

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStoreRequest $request)
    {
        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;

        if ($request->has('assigned_to'))
        {
            $task->assigned_to = $request->assigned_to;
        }

        $task->created_by = auth()->user()->id;

        if ($request->has('parent_id'))
        {
            $task->parent_id = $request->parent_id;
        }

        if ($request->has('due_date'))
        {
            $task->due_date = $request->due_date;
        }

        $task->save();

        return  new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        if (auth()->user()->id !== $task->created_by)
        {
            return response()->json(['message' => 'You are not authorized to update this task'], 403);
        }

        $taskLog = new TaskLog();
        $taskLog->task_id = $task->id;

        if ($request->has('title'))
        {
            $taskLog->title = $task->title;
            $task->title = $request->title;
        }

        if ($request->has('description'))
        {
            $taskLog->description = $task->description;
            $task->description = $request->description;
        }

        if ($request->has('assigned_to'))
        {
            $taskLog->assigned_to = $task->assigned_to;
            $task->assigned_to = $request->assigned_to;
        }

        if ($request->has('developed_by'))
        {
            $taskLog->developed_by = $task->developed_by;
            $task->developed_by = $request->developed_by;
        }

        if ($request->has('tested_by'))
        {
            $taskLog->tested_by = $task->tested_by;
            $task->tested_by = $request->tested_by;
        }

        if ($request->has('parent_id'))
        {
            $task->parent_id = $request->parent_id;
        }

        if ($request->has('due_date'))
        {
            $taskLog->due_date = $task->due_date;
            $task->due_date = $request->due_date;
        }

        if ($request->has('status'))
        {
            $taskLog->status = $task->status;
            $task->status = $request->status;
        }

        $task->save();
        $taskLog->save();

        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);

    }

    public function PoReviewTasks()
    {
        $completed_tasks = Task::where('status', 'PO_REVIEW')
                                ->where('created_by', auth()->user()->id)
                                ->get();

        return TaskResource::collection($completed_tasks);
    }

    public function assignTask(Task $task, User $user)
    {
        if(auth()->user()->id !== $task->created_by)
        {
            return response()->json(['message' => 'You are not authorized to assign this task'], 403);
        }

        if ($user->role->name !== 'developer' && $user->role->name !== 'tester')
        {
            return response()->json(['message' => 'User that you want to assign the task to must be developer or tester'], 400);
        }

        if ($task->assigned_to === $user->id)
        {
            return response()->json(['message' => 'Task is already assigned to this user'], 400);
        }

        $taskLog = new TaskLog();

        if ($user->role->name === 'developer')
        {
            $taskLog->developed_by = $task->developed_by;
            $task->developed_by = $user->id;
        }

        elseif ($user->role->name === 'tester')
        {
            $taskLog->tested_by = $task->tested_by;
            $task->tested_by = $user->id;
        }

        $taskLog->task_id = $task->id;
        $taskLog->assigned_to = $task->assigned_to;
        $taskLog->save();

        $task->assigned_to = $user->id;
        $task->save();

        return response()->json(['message' => 'Task has been assigned successfully'], 200);
    }

    public function changeStatus(Request $request, Task $task)
    {
        $status = $request->status;
        if (!in_array($status, TaskStatusEnum::getValues()))
        {
            return response()->json(['message' => 'Invalid status'], 400);
        }

        //dd($status);

        if ($task->status === $status)
        {
            return response()->json(['message' => 'Task status is already ' . $status], 400);
        }

        $taskLog = new TaskLog();
        $taskLog->task_id = $task->id;
        $taskLog->status = $task->status;

        if (auth()->user()->role->id === 1 && $task->created_by === auth()->user()->id)
        {
            $task->status = $status;
            $task->save();
            $taskLog->save();

            return response()->json(['message' => 'Task status changed successfully'], 200);
        }

        elseif (auth()->user()->role->id === 2 && $task->developed_by === auth()->user()->id)
        {
            if ($task->status === TaskStatusEnum::TODO && $status === TaskStatusEnum::IN_PROGRESS || $task->status === TaskStatusEnum::IN_PROGRESS && $status === TaskStatusEnum::READY_FOR_TEST)
            {
                $task->status = $status;
                $task->save();
                $taskLog->save();

                return response()->json(['message' => 'Task status changed successfully'], 200);
            }
        }

        elseif (auth()->user()->role->id === 3 && $task->tested_by === auth()->user()->id)
        {
            if ($task->status === TaskStatusEnum::READY_FOR_TEST && $status === TaskStatusEnum::PO_REVIEW)
            {
                $task->status = $status;
                $task->save();
                $taskLog->save();

                return response()->json(['message' => 'Task status changed successfully'], 200);
            }
        }

        else
        {
            return response()->json(['message' => 'You are not authorized to change this task status'], 403);
        }
    }
}
