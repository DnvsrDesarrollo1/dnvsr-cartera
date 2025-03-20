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
        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('image_b64'); // Imagen en Base64
            $table->text('image_json'); // Metadatos en JSON
            $table->text('ci'); // Cédula de identidad
            $table->text('idepro'); // Relación con beneficiaries.idepro
            $table->text('request_status');
            $table->text('image_xml'); // XML asociado
            $table->timestamps(6); // Precisión 6 según el PDF

            $table->foreign('idepro')->references('idepro')->on('beneficiaries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
