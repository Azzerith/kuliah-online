<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('announcement_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            $table->unique(['announcement_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('announcement_recipients');
    }
};