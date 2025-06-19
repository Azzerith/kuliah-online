<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('assignment_id')->nullable()->constrained('assignments');
            $table->enum('metric_type', ['assignment', 'participation', 'attendance', 'overall']);
            $table->string('metric_name', 100);
            $table->decimal('metric_value', 5, 2);
            $table->decimal('max_value', 5, 2);
            $table->decimal('percentile', 5, 2)->nullable();
            $table->timestamp('calculated_at');
            
            $table->index(['student_id', 'class_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_performance_metrics');
    }
};