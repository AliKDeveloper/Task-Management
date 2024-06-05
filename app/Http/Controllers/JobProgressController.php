<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class JobProgressController extends Controller
{
    public function getImportProgress($batchId)
    {
        $batch = Bus::findBatch($batchId);

        if (!$batch) {
            return response()->json(['error' => 'Batch not found.'], 404);
        }

        return response()->json([
            'id' => $batch->id,
            'name' => $batch->name,
            'totalJobs' => $batch->totalJobs,
            'pendingJobs' => $batch->pendingJobs,
            'failedJobs' => $batch->failedJobs,
            'processedJobs' => $batch->processedJobs(),
            'progress' => $batch->progress(),
        ]);
    }
}
