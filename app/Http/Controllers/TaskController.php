<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\TasksResource;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ImportTasksJob;
use League\Csv\Writer;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskAssigned;
use Illuminate\Support\Facades\Bus;

class TaskController extends Controller
{
    public function index()
    {
        //$tasks = Task::with('assignedUser', 'subtasks', 'logs')->get();
        $tasks = Task::all();
        return TasksResource::collection($tasks);
    }

    public function store(Request $request)
    {
        Gate::authorize('isProductOwner', auth()->user());

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date'
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'assigned_to' => null,
            'created_by'=> auth()->id(),
            'status' => 'TODO'
        ]);

        TaskLog::create([
            'task_id' => $task->id,
            'log' => 'Task created'
        ]);

        return response()->json($task);
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);

        return TasksResource::collection($task);
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('isProductOwner', auth()->user());

        $task = Task::findOrFail($id);
        $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'status' => 'string|in:TODO,IN_PROGRESS,READY_FOR_TEST,PO_REVIEW,DONE,REJECTED'
        ]);

        if ($request->has('title')) $task->title = $request->title;
        if ($request->has('description')) $task->description = $request->description;
        if ($request->has('status')) $task->status = $request->status;

        $task->save();

        TaskLog::create([
            'task_id' => $task->id,
            'log' => 'Task updated: ' . json_encode($request->all())
        ]);

        if ($task->assigned_to) {
            Mail::to($task->assignedUser->email)->send(new TaskAssigned($task));
        }

        return response()->json($task);
    }

    public function destroy($id)
    {
        Gate::authorize('isProductOwner', auth()->user());

        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['status' => true, 'message'=>'Task has been deleted successfully'], 200);
    }

    protected function assignTask($taskId, $userId)
    {
        Gate::authorize('isProductOwner', auth()->user());

        $task = Task::findOrFail($taskId);
        $task->assigned_to = $userId;
        $task->save();

        return response()->json($task);
    }

    // Change task status based on user role
    public function changeStatus($taskId, $status)
    {
        $allowedStatuses = [];
        $task = Task::findOrFail($taskId);

        if (auth()->user()->role == 'product_owner')
        {
            $allowedStatuses[] = 'DONE';
            $allowedStatuses[] = 'REJECTED';
            $allowedStatuses[] = 'IN_PROGRESS';
        }
        elseif (auth()->user()->role == 'developer')
        {
            $allowedStatuses[] = 'TODO';
            $allowedStatuses[] = 'IN_PROGRESS';
            $allowedStatuses[] = 'READY_FOR_TEST';
        }
        elseif (auth()->user()->role == 'tester')
        {
            $allowedStatuses[] = 'PO_REVIEW';
        }

        else
        {
            return response()->json(['status' => false, 'message' => 'Invalid status']);
        }

        // =============================================================
        if (in_array($status, $allowedStatuses))
        {
            $task->status = $status;
            $task->save();

            if ($status == 'PO_REVIEW')
            {
                $userId = User::where('id', $task->created_by)->first()->id;
                $this->assignTask($taskId, $userId);

                $this->assignTask($taskId, $userId);
            }

            elseif ($status == 'READY_FOR_TEST')
            {
                $testerWithFewestTasks = User::withCount('tasks')
                    ->where('role', 'tester')
                    ->orderBy('tasks_count')
                    ->first(['id as tester_id']);

                if ($testerWithFewestTasks)
                {
                    $testerId = $testerWithFewestTasks->tester_id;
                    $this->assignTask($taskId, $testerId);
                }

            }

            elseif (auth()->user()->role == 'product_owner' && in_array($status, ['IN_PROGRESS', 'DONE']))
            {
                $userId = User::where('id', $task->assigned_to)->first()->id;
                $this->assignTask($taskId, $userId);
            }

            if ($task->assigned_to) {
                Mail::to($task->assignedUser->email)->send(new TaskAssigned($task));
            }

            return TasksResource::collection($task);
        }
        else
        {
            return response()->json(['status' => false, 'message' => 'You are not allowed to perform this action']);
        }
    }

    public function getPoReviewTasks()
    {
        Gate::authorize('isProductOwner', auth()->user());

        $tasks = Task::where('status', 'PO_REVIEW')->get();
        return response()->json($tasks);

    }

    // Other methods for import, export, progress tracking etc.
    public function import(Request $request)
    {
        Gate::authorize('isProductOwner', auth()->user());

        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $filePath = $request->file('file')->store('imports');

        $batch = Bus::batch([
            new ImportTasksJob(storage_path('app/' . $filePath))
        ])->dispatch();

        return response()->json(['batch_id' => $batch->id]);
    }

    public function export()
    {
        Gate::authorize('isProductOwner', auth()->user());

        $tasks = Task::all();
        $csv = Writer::createFromString('');

        $csv->insertOne(['id', 'title', 'description', 'due_date', 'assigned_to', 'status']);

        foreach ($tasks as $task) {
            $csv->insertOne([
                $task->id,
                $task->title,
                $task->description,
                $task->due_date,
                $task->assigned_to,
                $task->status
            ]);
        }

        $fileName = 'tasks_export_' . now()->format('Ymd_His') . '.csv';
        Storage::put($fileName, $csv->toString());

        return response()->download(storage_path('app/' . $fileName))->deleteFileAfterSend(true);
    }

    public function progress(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|string'
        ]);

        $batch = Bus::findBatch($request->batch_id);

        if ($batch) {
            return response()->json([
                'id' => $batch->id,
                'total_jobs' => $batch->totalJobs,
                'pending_jobs' => $batch->pendingJobs,
                'failed_jobs' => $batch->failedJobs,
                'processed_jobs' => $batch->processedJobs(),
                'progress' => $batch->progress()
            ]);
        }

        return response()->json(['message' => 'Batch not found'], 404);
    }

}
