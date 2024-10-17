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
        Schema::create('readjustments', function (Blueprint $table) {
            $table->id();
            $table->string('idepro')->nullable();
            $table->date('fecha_ppg')->nullable();
            $table->string('prppgnpag')->nullable();
            $table->double('prppgcapi')->nullable();
            $table->double('prppginte')->nullable();
            $table->double('prppggral')->nullable();
            $table->double('prppgsegu')->nullable();
            $table->double('prppgotro')->nullable();
            $table->double('prppgcarg')->nullable();
            $table->double('prppgtota')->nullable();
            $table->string('prppgahor')->nullable();
            $table->string('prppgmpag')->nullable();
            $table->string('estado')->default('ACTIVO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('readjustments');
    }
};
