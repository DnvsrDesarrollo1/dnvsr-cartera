<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BeneficiaryStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:beneficiary-status';

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
        $beneficiaries = \App\Models\Beneficiary::all();

        foreach ($beneficiaries as $b) {
            if ($b->plans()->where('estado', 'VENCIDO')->count() > 0) {
                $b->estado = 'VENCIDO';
            }
            if ($b->plans()->where('estado', 'EJECUCION')->count() > 0) {
                $b->estado = 'EJECUCION';
            }
            if ($b->plans()->where('estado', 'VENCIDO')->count() == 0 AND $b->plans()->where('estado', 'EJECUCION')->count() == 0) {
                $b->estado = 'VIGENTE';
            }
        }

        Log/*  */::info('Beneficiary status updated');
    }
}
