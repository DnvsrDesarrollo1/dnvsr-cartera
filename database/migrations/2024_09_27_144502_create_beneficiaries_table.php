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
            $table->string('ci', 255)->unique();
            $table->string('complemento', 255)->nullable();
            $table->string('expedido', 255);
            $table->string('mail', 255);
            $table->string('estado', 255);
            $table->string('entidad_financiera', 255);
            $table->string('cod_proy', 255); // Clave foránea hacia projects (proy_cod)
            $table->string('idepro', 255);
            $table->string('cod_promotor', 255);
            $table->string('cod_cristorey', 255);
            $table->string('cod_fondesif', 255);
            $table->string('cod_smp', 255);
            $table->string('proyecto', 255);
            $table->string('genero', 255);
            $table->date('fecha_nacimiento');
            $table->double('monto_credito', 8, 2);
            $table->double('monto_activado', 8, 2);
            $table->double('total_activado', 8, 2);
            $table->double('gastos_judiciales', 8, 2)->default(0);
            $table->double('saldo_credito', 8, 2);
            $table->double('monto_recuperado', 8, 2)->default(0);
            $table->date('fecha_activacion');
            $table->integer('plazo_credito');
            $table->text('tasa_interes');
            $table->string('departamento', 255);
            $table->bigInteger('user_id')->nullable();
            $table->date('fecha_extendida')->nullable();
            $table->timestamps(6);

            // Clave foránea hacia projects
            $table->foreign('cod_proy')
                ->references('proy_cod')
                ->on('projects')
                ->onDelete('cascade');
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
