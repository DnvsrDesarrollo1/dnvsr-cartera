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
            $table->id();
                $table->integer('numpago')->nullable()->default(0);
                $table->string('numtramite', 50)->nullable()->default(null);
                $table->string('numprestamo', 50)->nullable()->default(null);
                $table->date('fecha_pago')->nullable()->default(null);
                $table->string('descripcion', 50)->nullable()->default(null);
                $table->double('montopago', 11, 2)->nullable()->default(null);
                $table->time('hora_pago')->nullable()->default(null);
                $table->date('prtdtfpro')->nullable()->default(null);
                $table->string('agencia_pago', 50)->nullable()->default('');
                $table->string('depto_pago', 20)->nullable()->default('');
                $table->string('obs_pago', 100)->nullable()->default(null);
            $table->timestamps();
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
