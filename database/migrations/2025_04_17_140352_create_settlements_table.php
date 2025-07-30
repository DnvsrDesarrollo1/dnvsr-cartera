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
            $table->bigIncrements('id')->startingValue(1);

            $table->double('capital_inicial')->nullable();
            $table->double('capital_final')->nullable();
            $table->double('capital_diferido')->nullable();
            $table->double('interes')->nullable();
            $table->double('interes_devengado')->nullable();
            $table->double('interes_diferido')->nullable();
            $table->double('seguro')->nullable();
            $table->double('seguro_devengado')->nullable();
            $table->double('gastos_judiciales')->nullable();
            $table->double('gastos_administrativos')->nullable();
            $table->double('otros')->nullable();
            $table->double('descuento')->nullable();

            $table->text('plan_de_pagos')->nullable();

            $table->enum(
                'estado',
                [
                    'pendiente',
                    'aprobado',
                    'ejecutado'
                ]
            )->nullable();

            $table->text('comentarios')->nullable();
            $table->text('observaciones')->nullable();

            $table->string('anexos')->nullable();

            $table->unsignedBigInteger('beneficiary_id')->unique();
            $table->unsignedBigInteger('user_id');

            /* $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('beneficiary_id')
                ->references('id')
                ->on('beneficiaries')
                ->onDelete('cascade'); */

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
