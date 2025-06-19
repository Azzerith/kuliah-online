<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments');
            $table->foreignId('student_id')->constrained('users');
            $table->decimal('total_score', 5, 2)->nullable();
            $table->enum('submission_status', ['not_submitted', 'draft', 'submitted', 'late', 'graded'])->default('not_submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            
            $table->unique(['assignment_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignment_submissions');
    }
};