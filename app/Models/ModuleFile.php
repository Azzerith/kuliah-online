<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'type'
    ];

    // Relationships
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    // Methods
    public function getFileSizeInMbAttribute()
    {
        return round($this->file_size / 1024, 2);
    }

    public function getFileIconAttribute()
    {
        switch ($this->file_type) {
            case 'pdf':
                return 'fa-file-pdf';
            case 'ppt':
                return 'fa-file-powerpoint';
            case 'video':
                return 'fa-file-video';
            default:
                return 'fa-file';
        }
    }
}