<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'total_score',
        'submission_status',
        'submitted_at',
        'graded_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime'
    ];

    // Relationships
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasManyThrough(
            StudentAnswer::class,
            Assignment::class,
            'id',
            'assignment_id',
            'assignment_id',
            'id'
        )->where('student_id', $this->student_id);
    }

    // Methods
    public function calculateTotalScore()
    {
        $total = $this->answers()->sum('score');
        $this->update(['total_score' => $total]);
        return $total;
    }

    public function isLate()
    {
        return $this->assignment->due_date && 
               $this->submitted_at && 
               $this->submitted_at->gt($this->assignment->due_date);
    }
}