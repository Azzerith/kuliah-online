<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'expires_at',
        'max_usage',
        'current_usage',
        'created_by'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Methods
    public function isValid()
    {
        return $this->expires_at->isFuture() && 
               $this->current_usage < $this->max_usage;
    }
}