<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EventScheduleService;

class ProcessEvents extends Command
{
    protected $signature = 'events:process';
    protected $description = 'Process events and create repeats';

    public function handle(EventScheduleService $service)
    {
        $service->processEvents();
    }
}