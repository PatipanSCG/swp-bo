<?php
// app/Models/WorkImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WorkImage extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv_secondary'; // เปลี่ยนตามชื่อ connection ของคุณ

    protected $table = 'WorkImage';

    protected $fillable = [
        'workid',
        'type', 
        'NozzleID',
        'imagename'
    ];

    protected $casts = [
        'workid' => 'integer',
        'type' => 'integer',
        'NozzleID' => 'integer',
    ];

    /**
     * Get the full path to the image
     */
    public function getImagePathAttribute()
    {
        return Storage::url('work_images/' . $this->imagename);
    }

    /**
     * Get the full URL to the image
     */
    public function getImageUrlAttribute()
    {
        return asset('storage/work_images/' . $this->imagename);
    }

    /**
     * Scope to filter by work ID
     */
    public function scopeByWorkId($query, $workId)
    {
        return $query->where('workid', $workId);
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by nozzle ID
     */
    public function scopeByNozzleId($query, $nozzleId)
    {
        return $query->where('NozzleID', $nozzleId);
    }

    /**
     * Delete the image file when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($workImage) {
            if (Storage::exists('work_images/' . $workImage->imagename)) {
                Storage::delete('work_images/' . $workImage->imagename);
            }
        });
    }
}