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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 255);
            $table->string('ci', 255)->nullable();
            $table->string('complemento', 255)->nullable();
            $table->string('expedido', 255)->nullable();
            $table->string('mail', 255)->nullable();
            $table->string('estado', 255)->nullable();
            $table->string('entidad_financiera', 255)->nullable();
            $table->string('cod_proy', 255)->nullable(); // Clave foránea hacia projects (proy_cod)
            $table->string('idepro', 255)->nullable();
            $table->string('cod_promotor', 255)->nullable();
            $table->string('cod_cristorey', 255)->nullable();
            $table->string('cod_fondesif', 255)->nullable();
            $table->string('cod_smp', 255)->nullable();
            $table->string('proyecto', 255)->nullable();
            $table->string('genero', 255)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->double('monto_credito')->nullable();
            $table->double('monto_activado')->nullable();
            $table->double('total_activado')->nullable();
            $table->double('gastos_judiciales')->nullable()->default(0);
            $table->double('saldo_credito');
            $table->double('monto_recuperado')->nullable()->default(0);
            $table->date('fecha_activacion')->nullable();
            $table->integer('plazo_credito')->nullable();
            $table->text('tasa_interes')->nullable();
            $table->string('departamento', 255);
            $table->bigInteger('user_id')->nullable();
            $table->date('fecha_extendida')->nullable();
            $table->timestamps(6);

            // Clave foránea hacia projects
            /* $table->foreign('cod_proy')
                ->references('proy_cod')
                ->on('projects')
                ->onDelete('cascade'); */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
