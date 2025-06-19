<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nidn_nim', 20)->nullable()->comment('NIM untuk mahasiswa, NIDN untuk dosen');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'dosen', 'mahasiswa']);
            $table->boolean('is_asisten')->default(false);
            $table->string('profile_photo')->nullable();
            $table->string('phone', 20)->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index('nidn_nim');
            $table->index('role');
            $table->index('is_asisten');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};