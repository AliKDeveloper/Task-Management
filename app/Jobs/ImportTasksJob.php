<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader;

class ImportTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle()
    {
        $csv = Reader::createFromPath($this->filePath, 'r');
        $csv->setHeaderOffset(0); // Assumes the first row of the CSV contains the headers
        $records = $csv->getRecords();

        foreach ($records as $record) {
            Task::create([
                'title' => $record['title'],
                'description' => $record['description'],
                'due_date' => $record['due_date'],
                'assigned_to' => $record['assigned_to'],
                'status' => $record['status'] ?? 'TODO',
            ]);
        }
    }
}
