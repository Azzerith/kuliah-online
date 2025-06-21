<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nidn_nim',
        'name',
        'email',
        'password',
        'role',
        'is_asisten',
        'profile_photo',
        'phone',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_asisten' => 'boolean'
    ];

    protected $attributes = [
        'status' => 'non-aktif'
    ];

    // Relationships
    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'class_members')
            ->withPivot('is_asisten')
            ->withTimestamps();
    }

    public function taughtClasses()
    {
        return $this->hasMany(ClassModel::class, 'lecturer_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class, 'student_id');
    }

    public function announcements()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_recipients')
            ->withPivot('is_read', 'read_at')
            ->withTimestamps();
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function todoProgress()
    {
        return $this->hasMany(TodoProgress::class, 'student_id');
    }

    // Scopes
    public function scopeStudents($query)
    {
        return $query->where('role', 'mahasiswa');
    }

    public function scopeLecturers($query)
    {
        return $query->where('role', 'dosen');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Methods
    public function isDosenOrAsistenForClass($class)
    {
        return $this->role === 'dosen' || 
               ($this->role === 'mahasiswa' && $this->is_asisten && 
                $this->classes()->where('class_id', $class->id)->exists());
    }
}