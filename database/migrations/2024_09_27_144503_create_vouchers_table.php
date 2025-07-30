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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->bigIncrements('id')->startingValue(1);
            $table->integer('numpago')->nullable();
            $table->string('numtramite', 50)->nullable();
            $table->string('numprestamo', 50)->nullable();
            $table->date('fecha_pago')->nullable();
            $table->text('descripcion')->nullable();
            $table->double('montopago')->nullable();
            $table->time('hora_pago', 6)->nullable();
            $table->date('prtdtfpro')->nullable();
            $table->string('agencia_pago', 150)->nullable();
            $table->string('depto_pago', 20)->nullable();
            $table->text('obs_pago')->nullable();
            $table->timestamps(6); // created_at y updated_at con precisión 6

            //$table->foreign('numprestamo')
            //    ->references('idepro')
            //    ->on('beneficiaries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
