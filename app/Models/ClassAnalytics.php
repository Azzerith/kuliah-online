<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'analytics_date',
        'total_assignments',
        'average_score',
        'completion_rate',
        'participation_rate',
        'top_performer_id',
        'most_improved_id'
    ];

    protected $casts = [
        'analytics_date' => 'date'
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function topPerformer()
    {
        return $this->belongsTo(User::class, 'top_performer_id');
    }

    public function mostImproved()
    {
        return $this->belongsTo(User::class, 'most_improved_id');
    }
}