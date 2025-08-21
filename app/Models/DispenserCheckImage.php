<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DispenserCheckImage extends Model
{
   use HasFactory;

    protected $connection = 'sqlsrv_secondary';
    protected $table = 'DispenserCheckImages';
    protected $primaryKey = 'id';

    protected $fillable = [
        'dispenser_check_detail_id',
        'image_type',
        'imagename',
        'image_description'
    ];

    protected $casts = [
        'dispenser_check_detail_id' => 'integer',
    ];

    /**
     * ความสัมพันธ์กับ DispenserCheckDetail
     */
    public function checkDetail()
    {
        return $this->belongsTo(DispenserCheckDetail::class, 'dispenser_check_detail_id');
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        return asset("storage/dispenser_checks/{$this->imagename}");
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        $thumbnailPath = "dispenser_checks/thumbnails/{$this->imagename}";
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return asset("storage/{$thumbnailPath}");
        }
        return $this->image_url;
    }

    /**
     * Auto delete files when model deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            // ลบไฟล์หลัก
            if (Storage::disk('public')->exists("dispenser_checks/{$image->imagename}")) {
                Storage::disk('public')->delete("dispenser_checks/{$image->imagename}");
            }
            
            // ลบ thumbnail
            if (Storage::disk('public')->exists("dispenser_checks/thumbnails/{$image->imagename}")) {
                Storage::disk('public')->delete("dispenser_checks/thumbnails/{$image->imagename}");
            }
        });
    }
}
