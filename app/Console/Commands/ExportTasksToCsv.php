<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;

class ExportTasksToCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:export-csv {filepath=tasks_export.csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tasks = Task::all();
        $filepath = $this->argument('filepath');

        // Create a CSV writer instance
        $csv = Writer::createFromString();

        // Add the header row
        $csv->insertOne(['ID', 'Title', 'Description', 'Assigned To', 'Created By', 'Developed By', 'Tested By', 'Parent ID', 'Due Date', 'Status']);

        // Add the data rows
        foreach ($tasks as $task)
        {
            $csv->insertOne([
                $task->id,
                $task->title,
                $task->description,
                $task->assigned_to,
                $task->created_by,
                $task->developed_by,
                $task->tested_by,
                $task->parent_id,
                $task->due_date,
                $task->status
            ]);
        }

        // Store the CSV file
        Storage::put($filepath, $csv->toString());

        $this->info('Task exported successfully to ' . $filepath);
    }
}
