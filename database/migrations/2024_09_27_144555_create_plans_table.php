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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
                $table->string('idepro', 255)->notNull();
                $table->date('fecha_ppg')->notNull();
                $table->string('prppgnpag', 255)->notNull();
                $table->double('prppgcapi')->notNull();
                $table->double('prppginte')->notNull();
                $table->double('prppggral')->notNull();
                $table->double('prppgsegu')->notNull();
                $table->double('prppgotro')->notNull();
                $table->double('prppgcarg')->notNull();
                $table->double('prppgtota')->notNull();
                $table->string('prppgahor', 255)->notNull();
                $table->string('prppgmpag', 255)->notNull();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
