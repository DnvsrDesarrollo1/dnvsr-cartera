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
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('proy_cod', 255)->unique(); // Clave única para relación
            $table->string('cod_proy_credito', 255);
            $table->string('proy_nombre', 255);
            $table->string('proy_subprograma', 255);
            $table->string('proy_numActa', 255);
            $table->date('proy_fechaAprobacion');
            $table->integer('proy_numViviendas');
            $table->string('proy_estado', 255);
            $table->string('proy_modalidad', 255);
            $table->string('entidad_inter_finan', 255);
            $table->string('proy_programa', 255);
            $table->date('fecha_ini_obra')->nullable();
            $table->date('fecha_fin_obra')->nullable();
            $table->integer('proy_viv_concluidas');
            $table->integer('proy_viv_cartera');
            $table->string('proy_componente', 255);
            $table->string('proy_depto', 255);
            $table->string('proy_provincia', 255);
            $table->string('proy_municipio', 255);
            $table->string('proy_ubicacion', 255);
            $table->double('proy_avance_finan', 8, 2);
            $table->double('proy_avance_fis', 8, 2);
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
