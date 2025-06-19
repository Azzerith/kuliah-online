<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('class_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes');
            $table->date('analytics_date');
            $table->integer('total_assignments');
            $table->decimal('average_score', 5, 2);
            $table->decimal('completion_rate', 5, 2);
            $table->decimal('participation_rate', 5, 2);
            $table->foreignId('top_performer_id')->nullable()->constrained('users');
            $table->foreignId('most_improved_id')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['class_id', 'analytics_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_analytics');
    }
};