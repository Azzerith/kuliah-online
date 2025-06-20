<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('class_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('is_asisten')->default(false);
            $table->timestamps();
            
            $table->unique(['class_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_members');
    }
};