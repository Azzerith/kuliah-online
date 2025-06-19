<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('module_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules');
            $table->string('file_name');
            $table->string('file_path');
            $table->enum('file_type', ['ppt', 'pdf', 'video', 'other']);
            $table->integer('file_size')->comment('Dalam KB');
            $table->enum('type', ['teori', 'praktikum', 'video']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('module_files');
    }
};