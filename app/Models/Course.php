<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_studi_id',
        'code',
        'name',
        'sks',
        'semester',
        'description'
    ];

    // Relationships
    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }

    // Methods
    public function getFullCodeAttribute()
    {
        return "{$this->programStudi->code}-{$this->code}";
    }
}