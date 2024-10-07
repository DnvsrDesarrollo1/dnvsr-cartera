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
            $table->id();
                $table->string('nombre', 255)->notNull();
                $table->string('ci', 255)->notNull();
                $table->string('complemento', 255)->nullable()->default(null);
                $table->string('expedido', 255)->nullable()->default(null);
                $table->string('mail', 255)->notNull();
                $table->string('estado', 255)->notNull();
                $table->string('entidad_financiera', 255)->notNull();
                $table->string('cod_proy', 255)->notNull();
                $table->string('idepro', 255)->nullable()->default(null);
                $table->string('cod_promotor', 255)->nullable()->default(null);
                $table->string('cod_cristorey', 255)->nullable()->default(null);
                $table->string('cod_fondesif', 255)->nullable()->default(null);
                $table->string('cod_smp', 255)->nullable()->default(null);
                $table->string('proyecto', 255)->notNull();
                $table->string('genero', 255)->nullable()->default(null);
                $table->date('fecha_nacimiento')->notNull();
                $table->double('monto_credito')->notNull();
                $table->double('monto_activado')->notNull();
                $table->double('total_activado')->notNull();
                $table->double('saldo_credito')->notNull();
                $table->double('monto_recuperado')->notNull();
                $table->date('fecha_activacion')->notNull();
                $table->integer('plazo_credito')->notNull();
                $table->integer('tasa_interes')->notNull();
                $table->string('departamento', 255)->notNull();
            $table->timestamps();
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
