<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPerformanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'assignment_id',
        'metric_type',
        'metric_name',
        'metric_value',
        'max_value',
        'percentile',
        'calculated_at'
    ];

    protected $casts = [
        'calculated_at' => 'datetime'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    // Methods
    public function getPercentageAttribute()
    {
        return ($this->metric_value / $this->max_value) * 100;
    }
}