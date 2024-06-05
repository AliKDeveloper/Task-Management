<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ImportTasksJob;

class ImportTasksFromCsv extends Command
{
    protected $signature = 'tasks:import {file}';
    protected $description = 'Import tasks from a CSV file into the database asynchronously using batch jobs';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!Storage::exists($filePath)) {
            $this->error("File does not exist at path: {$filePath}");
            return 1;
        }

        $batch = Bus::batch([
            new ImportTasksJob(storage_path('app/' . $filePath)),
        ])->dispatch();

        $this->info("Tasks import initiated. Batch ID: {$batch->id}");

        return 0;
    }
}
