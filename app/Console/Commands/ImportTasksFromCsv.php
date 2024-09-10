<?php

namespace App\Console\Commands;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Console\Command;
use League\Csv\Reader;

class ImportTasksFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:import-csv {filepath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import tasks from a CSV file';

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filepath = $this->argument('filepath');
        $csv = Reader::createFromPath($filepath);

        // Assuming first row contains headers
        $csv->setHeaderOffset(0);
        $headers = $csv->getHeader();

        // Loop through the remaining rows
        foreach ($csv->getRecords() as $row)
        {
            $taskData = array_combine($headers, $row);
            Task::create([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'assigned_to' => $taskData['assigned_to'] === '' ? null : $taskData['assigned_to'],
                'created_by' => $taskData['created_by'] === '' ? null : $taskData['created_by'],
                'developed_by' => $taskData['developed_by'] === '' ? null : $taskData['developed_by'],
                'tested_by' => $taskData['tested_by'] === '' ? null : $taskData['tested_by'],
                'parent_id' => $taskData['parent_id'] === '' ? null : $taskData['parent_id'],
                'due_date' => $taskData['due_date'] === '' ? null : $taskData['due_date'],
                'status' => $taskData['status'] === '' ? TaskStatusEnum::TODO->value : $taskData['status'],
                'created_at' => $taskData['created_at'] === '' ? now() : $taskData['created_at'],
                'updated_at' => $taskData['updated_at'] === '' ? now() : $taskData['updated_at'],
            ]);
        }

        $this->info('Tasks imported successfully from ' . $filepath);
    }
}
