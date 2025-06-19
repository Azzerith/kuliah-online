<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoList extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'meeting_number',
        'title',
        'description',
        'created_by'
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function progress()
    {
        return $this->hasMany(TodoProgress::class);
    }

    // Methods
    public function getStudentProgress($studentId)
    {
        return $this->progress()->where('student_id', $studentId)->first();
    }
}