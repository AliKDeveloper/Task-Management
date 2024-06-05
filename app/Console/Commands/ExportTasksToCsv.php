<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use App\Models\Task;

class ExportTasksToCsv extends Command
{
    protected $signature = 'tasks:export {filename=tasks_export.csv}';
    protected $description = 'Export tasks to a CSV file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $tasks = Task::all();

        // Create a CSV writer instance
        $csv = Writer::createFromString('');
        $csv->insertOne(['ID', 'Title', 'Description', 'Due Date', 'Assigned To', 'Status']);

        // Insert tasks into the CSV
        foreach ($tasks as $task) {
            $csv->insertOne([
                $task->id,
                $task->title,
                $task->description,
                $task->assigned_to,
                $task->due_date,
                $task->status,
                $task->created_at
            ]);
        }

        // Get the filename from the command argument
        $filename = $this->argument('filename');

        // Store the CSV content in the specified file
        Storage::put($filename, $csv->toString());

        $this->info("Tasks exported successfully to {$filename}");
    }
}
