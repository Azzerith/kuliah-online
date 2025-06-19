<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $fillable = [
        'course_id',
        'academic_period_id',
        'lecturer_id',
        'name',
        'class_code',
        'meeting_schedule',
        'meeting_link',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'class_members')
            ->withPivot('is_asisten')
            ->withTimestamps();
    }

    public function students()
    {
        return $this->members()->wherePivot('is_asisten', false);
    }

    public function assistants()
    {
        return $this->members()->wherePivot('is_asisten', true);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function todoLists()
    {
        return $this->hasMany(TodoList::class);
    }

    public function analytics()
    {
        return $this->hasMany(ClassAnalytics::class);
    }

    // Methods
    public function generateClassCode()
    {
        $this->class_code = strtoupper(substr(md5(uniqid()), 0, 6));
        $this->save();
    }
}