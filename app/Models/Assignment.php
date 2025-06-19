<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'module_id',
        'title',
        'description',
        'assignment_type',
        'question_type',
        'due_date',
        'total_points',
        'is_published',
        'created_by'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_published' => 'boolean'
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(AssignmentQuestion::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Methods
    public function isPastDue()
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function getStudentSubmission($studentId)
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }
}