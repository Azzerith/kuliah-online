<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('program_studi', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->enum('jenjang', ['D3', 'S1', 'S2', 'S3']);
            $table->string('fakultas');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('program_studi');
    }
};