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
            $table->bigIncrements('id')->startingValue(1);
            $table->string('nombre_proyecto')->unique();
            $table->string('subprograma')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('observaciones')->nullable();
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
