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

                $table->string('nombre');
                $table->string('ci')->unique();
                $table->string('complemento')->nullable();
                $table->string('expedido');
                $table->string('mail')->nullable();
                $table->string('estado');
                $table->string('entidad_financiera');
                $table->string('cod_proy');
                $table->string('idepro')->unique();
                $table->string('cod_promotor')->nullable();
                $table->string('cod_cristorey')->nullable();
                $table->string('cod_fondesif')->nullable();
                $table->string('cod_smp')->nullable();
                $table->string('proyecto');
                $table->enum('genero', ['M', 'F', 'Otro']);
                $table->date('fecha_nacimiento');
                $table->decimal('monto_credito', 10, 2);
                $table->decimal('monto_activado', 10, 2);
                $table->decimal('total_activado', 10, 2);
                $table->decimal('gastos_judiciales', 10, 2)->default(0);
                $table->decimal('saldo_credito', 10, 2);
                $table->decimal('monto_recuperado', 10, 2)->default(0);
                $table->date('fecha_activacion');
                $table->integer('plazo_credito');
                $table->decimal('tasa_interes', 5, 2);
                $table->string('departamento');
                $table->timestamps();

            $table->foreign('cod_proy')->references('cod_proy_credito')->on('projects');
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
