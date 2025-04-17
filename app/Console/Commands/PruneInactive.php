<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PruneInactive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean al INACTIVO items from Plans, Helpers and Readjustments.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \App\Models\Plan::where('estado', 'INACTIVO')->delete();
        \App\Models\Helper::where('estado', 'INACTIVO')->delete();
        \App\Models\Readjustment::where('estado', 'INACTIVO')->delete();

        Log::info('Pruned inactive records');
    }
}
