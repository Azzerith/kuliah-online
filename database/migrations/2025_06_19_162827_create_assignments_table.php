<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('module_id')->nullable()->constrained('modules');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('assignment_type', ['latihan', 'laporan', 'responsi', 'todo']);
            $table->enum('question_type', ['pilihan_ganda', 'essay_singkat', 'lengkapi_kode', 'file_upload'])->nullable();
            $table->dateTime('due_date')->nullable();
            $table->decimal('total_points', 5, 2)->default(100.00);
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignments');
    }
};