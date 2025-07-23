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
        Schema::create('helpers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('idepro', 255)->nullable(); // Relación con beneficiaries.idepro
            $table->smallInteger('indice');
            $table->double('capital');
            $table->double('interes');
            $table->date('vencimiento');
            $table->string('estado', 50);
            $table->timestamps(6);
            $table->unsignedBigInteger('user_id')->nullable();

            // Clave foránea hacia beneficiaries.idepro
            /* $table->foreign('idepro')
                ->references('idepro')
                ->on('beneficiaries')
                ->onDelete('cascade');

            // Clave foránea hacia users (opcional)
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
        Schema::dropIfExists('helpers');
    }
};
