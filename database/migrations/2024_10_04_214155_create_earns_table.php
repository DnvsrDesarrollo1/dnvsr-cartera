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
        Schema::create('earns', function (Blueprint $table) {
            $table->bigIncrements('id')->startingValue(1);
            $table->string('idepro', 255); // Relación con beneficiaries.idepro
            $table->double('capital');
            $table->double('interes');
            $table->double('seguro');
            $table->string('estado', 255);
            $table->timestamps(0); // Precisión 0 según el PDF

            // Clave foránea hacia beneficiaries.idepro
            /* $table->foreign('idepro')
                ->references('idepro')
                ->on('beneficiaries')
                ->onDelete('cascade'); */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earns');
    }
};
