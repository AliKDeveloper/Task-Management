<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TestController extends Controller
{
    public function test()
    {
        Artisan::call('tasks:export-csv', ['filename' => 'test.csv']);
        return response()->download(storage_path('app/test.csv'));
    }

}
