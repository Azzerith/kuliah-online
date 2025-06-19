<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('todo_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_id')->constrained('todo_lists');
            $table->foreignId('student_id')->constrained('users');
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            
            $table->unique(['todo_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('todo_progress');
    }
};