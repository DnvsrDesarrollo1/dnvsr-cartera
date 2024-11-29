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
            $table->id();

                $table->string('idepro');
                $table->integer('indice');
                $table->decimal('capital', 10, 2);
                $table->decimal('interes', 10, 2);
                $table->date('vencimiento');
                $table->string('estado');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

            $table->foreign('idepro')->references('idepro')->on('beneficiaries');
            $table->foreign('user_id')->references('id')->on('users');
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
