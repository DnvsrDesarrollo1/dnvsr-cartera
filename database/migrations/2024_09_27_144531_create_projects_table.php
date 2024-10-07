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
            $table->id();
                $table->string('proy_cod', 255)->notNull();
                $table->string('cod_proy_credito', 255)->notNull();
                $table->string('proy_nombre', 255)->notNull();
                $table->string('proy_subprograma', 255)->notNull();
                $table->string('proy_numActa', 255)->notNull();
                $table->date('proy_fechaAprobacion')->notNull();
                $table->integer('proy_numViviendas')->notNull();
                $table->string('proy_estado', 255)->notNull();
                $table->string('proy_modalidad', 255)->notNull();
                $table->string('entidad_inter_finan', 255)->notNull();
                $table->string('proy_programa', 255)->notNull();
                $table->date('fecha_ini_obra')->notNull();
                $table->date('fecha_fin_obra')->notNull();
                $table->integer('proy_viv_concluidas')->notNull();
                $table->integer('proy_viv_cartera')->notNull();
                $table->string('proy_componente', 255)->notNull();
                $table->string('proy_depto', 255)->notNull();
                $table->string('proy_provincia', 255)->notNull();
                $table->string('proy_municipio', 255)->notNull();
                $table->string('proy_ubicacion', 255)->notNull();
                $table->double('proy_avance_finan')->notNull();
                $table->double('proy_avance_fis')->notNull();
            $table->timestamps();
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
