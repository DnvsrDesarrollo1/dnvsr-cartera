<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
                $table->string('numtramite', 255)->nullable()->default(null);
                $table->string('prtdtitem', 255)->nullable()->default('0');
                $table->string('prtdtttrn', 255)->nullable()->default('0');
                $table->string('numprestamo', 255)->nullable()->default(null);
                $table->date('fecha_pago')->nullable()->default(null);
                $table->string('prtdtpref', 255)->nullable()->default(null);
                $table->string('prtdtccon', 255)->nullable()->default(null);
                $table->string('prtdtdesc', 255)->nullable()->default(null);
                $table->double('montopago', 11, 2)->nullable()->default(null);
                $table->string('prtdtcmon', 255)->nullable()->default(null);
                $table->string('prtdtmrcb', 255)->nullable()->default(null);
                $table->string('prtdtuser', 255)->nullable()->default(null);
                $table->time('hora_pago')->nullable()->default(null);
                $table->date('prtdtfpro')->nullable()->default(null);
                $table->integer('prtdtnpag')->nullable()->default(null);
                $table->string('agencia_pago', 255)->nullable()->default(null);
                $table->string('depto_pago', 255)->nullable()->default(null);
                $table->string('observacion', 255)->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
