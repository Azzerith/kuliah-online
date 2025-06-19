<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'title',
        'content',
        'created_by',
        'is_pinned'
    ];

    protected $casts = [
        'is_pinned' => 'boolean'
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

    public function recipients()
    {
        return $this->belongsToMany(User::class, 'announcement_recipients')
            ->withPivot('is_read', 'read_at')
            ->withTimestamps();
    }

    // Scopes
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeGeneral($query)
    {
        return $query->whereNull('class_id');
    }

    // Methods
    public function markAsRead($userId)
    {
        $this->recipients()->updateExistingPivot($userId, [
            'is_read' => true,
            'read_at' => now()
        ]);
    }
}