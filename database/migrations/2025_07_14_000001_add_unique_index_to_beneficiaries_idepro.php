<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->unique('idepro');
        });
    }

    public function down()
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropUnique(['idepro']);
        });
    }
};
