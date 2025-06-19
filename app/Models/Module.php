<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
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

    public function files()
    {
        return $this->hasMany(ModuleFile::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    // Methods
    public function getMeetingTitleAttribute()
    {
        return "Pertemuan {$this->meeting_number}: {$this->title}";
    }
}