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
            $table->bigIncrements('id');
            $table->string('idepro', 255); // Relación con beneficiaries.idepro
            $table->date('fecha_ppg');
            $table->string('prppgnpag', 255);
            $table->double('prppgcapi', 8, 2);
            $table->double('prppginte', 8, 2);
            $table->double('prppggral', 8, 2);
            $table->double('prppgsegu', 8, 2);
            $table->double('prppgotro', 8, 2);
            $table->double('prppgcarg', 8, 2);
            $table->double('prppgtota', 8, 2);
            $table->string('prppgahor', 255);
            $table->string('prppgmpag', 255);
            $table->string('estado', 100);
            $table->unsignedBigInteger('user_id');
            $table->timestamps(6);

            // Clave foránea hacia beneficiaries.idepro
            $table->foreign('idepro')
                ->references('idepro')
                ->on('beneficiaries')
                ->onDelete('cascade');

            // Clave foránea hacia users (si es necesario)
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
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
