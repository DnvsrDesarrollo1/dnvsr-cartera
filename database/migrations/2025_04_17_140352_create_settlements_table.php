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
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();

            $table->double('capital_inicial');
            $table->double('capital_final');
            $table->double('capital_diferido');
            $table->double('interes');
            $table->double('interes_devengado');
            $table->double('interes_diferido');
            $table->double('seguro');
            $table->double('seguro_devengado');
            $table->double('gastos_judiciales');
            $table->double('gastos_administrativos');
            $table->double('otros');

            $table->text('plan_de_pagos');

            $table->enum(
                'estado',
                [
                    'pendiente',
                    'aprobado',
                    'ejecutado'
                ]
            );

            $table->text('comentarios')->nullable();
            $table->text('observaciones')->nullable();

            $table->string('anexos')->nullable();

            $table->unsignedBigInteger('beneficiary_id')->unique();
            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('beneficiary_id')
                ->references('id')
                ->on('beneficiaries')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
