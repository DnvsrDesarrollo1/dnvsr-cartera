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
            $table->bigIncrements('id')->startingValue(1);
            $table->string('numtramite', 255)->nullable();
            $table->string('prtdtitem', 255)->nullable();
            $table->string('prtdtttrn', 255)->nullable();
            $table->string('numprestamo', 255)->nullable(); // Relación con beneficiaries.idepro
            $table->date('fecha_pago')->nullable();
            $table->string('prtdtpref', 255)->nullable();
            $table->string('prtdtccon', 255)->nullable();
            $table->string('prtdtdesc', 255)->nullable();
            $table->double('montopago')->nullable();
            $table->string('prtdtcmon', 255)->nullable();
            $table->string('prtdtmrcb', 255)->nullable();
            $table->string('prtdtuser', 255)->nullable();
            $table->time('hora_pago', 6)->nullable();
            $table->date('prtdtfpro')->nullable();
            $table->integer('prtdtnpag')->nullable();
            $table->string('agencia_pago', 255)->nullable();
            $table->string('depto_pago', 255)->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps(6);

            // Clave foránea hacia beneficiaries.idepro (vía numprestamo)
            //$table->foreign('numprestamo')
            //    ->references('idepro')
            //    ->on('beneficiaries')
            //    ->onDelete('cascade');
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
