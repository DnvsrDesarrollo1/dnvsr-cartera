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
            $table->bigIncrements('id');
            $table->string('idepro', 255); // Relación con beneficiaries.idepro
            $table->date('fecha_ppg');
            $table->string('prppgnpag', 255);
            $table->double('prppgcapi')->nullable();
            $table->double('prppginte')->nullable();
            $table->double('prppggral')->nullable();
            $table->double('prppgsegu')->nullable();
            $table->double('prppgotro')->nullable();
            $table->double('prppgcarg')->nullable();
            $table->double('prppgtota')->nullable();
            $table->string('prppgahor', 255)->nullable();
            $table->string('prppgmpag', 255)->nullable();
            $table->string('estado', 50);
            $table->unsignedBigInteger('user_id');
            $table->timestamps(6);

            // Clave foránea hacia beneficiaries.idepro
            /* $table->foreign('idepro')
                ->references('idepro')
                ->on('beneficiaries')
                ->onDelete('cascade');

            // Clave foránea hacia users
            $table->foreign('user_id')
                ->references('id')
                ->on('users'); */
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
