<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_studi_id')->constrained('program_studi');
            $table->string('code', 10);
            $table->string('name');
            $table->tinyInteger('sks');
            $table->tinyInteger('semester');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->unique(['program_studi_id', 'code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
};