<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses');
            $table->foreignId('academic_period_id')->constrained('academic_periods');
            $table->foreignId('lecturer_id')->constrained('users');
            $table->string('name', 100);
            $table->string('class_code', 6)->unique();
            $table->string('meeting_schedule', 255)->nullable();
            $table->string('meeting_link', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('classes');
    }
};