<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'todo_id',
        'student_id',
        'status',
        'notes',
        'completed_at',
        'verified_by',
        'verified_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'verified_at' => 'datetime'
    ];

    // Relationships
    public function todo()
    {
        return $this->belongsTo(TodoList::class, 'todo_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Methods
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function verify($userId)
    {
        $this->update([
            'verified_by' => $userId,
            'verified_at' => now()
        ]);
    }
}