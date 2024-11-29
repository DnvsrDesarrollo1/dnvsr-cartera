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
            $table->id();
            $table->longText('image_b64')->nullable();
            $table->json('image_json')->nullable();
            $table->string('ci');
            $table->string('idepro');
            $table->string('request_status')->nullable();
            $table->longText('image_xml')->nullable();
            $table->timestamps();

            $table->foreign('idepro')->references('idepro')->on('beneficiaries');
            $table->foreign('ci')->references('ci')->on('beneficiaries');
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
