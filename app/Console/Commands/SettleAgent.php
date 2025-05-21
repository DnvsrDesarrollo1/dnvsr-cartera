<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SettleAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:settle-agent';

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
        $settlements = \App\Models\Settlement::all();

        foreach ($settlements as $s) {
            if ($s->estado == 'aprobado' && $s->updated_at->diffInDays(now()) >= 5)
            {
                
            }
        }
    }
}
