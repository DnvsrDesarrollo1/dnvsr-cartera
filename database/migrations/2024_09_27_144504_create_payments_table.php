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
            $table->bigIncrements('id');
            $table->string('numtramite', 255);
            $table->string('prtdtitem', 255);
            $table->string('prtdtttrn', 255);
            $table->string('numprestamo', 255); // Relación con beneficiaries.idepro
            $table->date('fecha_pago');
            $table->string('prtdtpref', 255);
            $table->string('prtdtccon', 255);
            $table->string('prtdtdesc', 255);
            $table->double('montopago', 8, 2);
            $table->string('prtdtcmon', 255);
            $table->string('prtdtmrcb', 255);
            $table->string('prtdtuser', 255);
            $table->time('hora_pago', 6);
            $table->date('prtdtfpro')->nullable();
            $table->integer('prtdtnpag');
            $table->string('agencia_pago', 255);
            $table->string('depto_pago', 255);
            $table->text('observacion')->nullable();
            $table->timestamps(6);

            // Clave foránea hacia beneficiaries.idepro (vía numprestamo)
            $table->foreign('numprestamo')
                ->references('idepro')
                ->on('beneficiaries')
                ->onDelete('cascade');
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
