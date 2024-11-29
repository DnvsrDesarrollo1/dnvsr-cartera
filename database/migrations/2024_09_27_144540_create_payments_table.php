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
                $table->string('numtramite');
                $table->string('numprestamo');
                $table->string('prtdtpref')->nullable();
                $table->string('prtdtccon')->nullable();
                $table->date('fecha_pago');
                $table->string('prtdtdesc')->nullable();
                $table->decimal('montopago', 10, 2);
                $table->string('prtdtuser')->nullable();
                $table->time('hora_pago')->nullable();
                $table->date('prtdtfpro')->nullable();
                $table->integer('prtdtnpag')->nullable();
                $table->string('depto_pago')->nullable();
                $table->text('observacion')->nullable();
                $table->timestamps();

            $table->foreign('numprestamo')->references('idepro')->on('beneficiaries');
            $table->foreign('numtramite')->references('numtramite')->on('vouchers');
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
