<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    use HasFactory;

    protected $table = 'program_studi';

    protected $fillable = [
        'name',
        'code',
        'jenjang',
        'fakultas'
    ];

    // Relationships
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Methods
    public function getFullNameAttribute()
    {
        return "{$this->jenjang} {$this->name}";
    }
}