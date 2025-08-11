<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'banners';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'image',
        'title',
        'description',
        'status',
        'location_id'
    ];

    public function status()
    {
        switch ($this->status) {
            case '1':
                $status = 'Aktif';
                break;
            case '2':
                $status = 'Tidak Aktif';
                break;
            default:
                $status = 'Invalid';
                break;
        }

        return $status;
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
