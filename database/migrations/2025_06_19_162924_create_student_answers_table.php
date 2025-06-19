<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments');
            $table->foreignId('question_id')->constrained('assignment_questions');
            $table->foreignId('student_id')->constrained('users');
            $table->text('answer')->nullable();
            $table->string('file_path')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->foreignId('corrected_by')->nullable()->constrained('users');
            $table->text('feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('corrected_at')->nullable();
            
            $table->unique(['assignment_id', 'question_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_answers');
    }
};