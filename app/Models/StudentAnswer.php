<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'question_id',
        'student_id',
        'answer',
        'file_path',
        'score',
        'corrected_by',
        'feedback',
        'submitted_at',
        'corrected_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'corrected_at' => 'datetime'
    ];

    // Relationships
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function question()
    {
        return $this->belongsTo(AssignmentQuestion::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function corrector()
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    // Methods
    public function isCorrect()
    {
        if ($this->question->isMultipleChoice()) {
            $correctOption = $this->question->options()
                ->where('is_correct', true)
                ->first();
                
            return $correctOption && $this->answer == $correctOption->id;
        }
        
        return false;
    }
}