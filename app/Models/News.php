<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'news';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'image',
        'title',
        'slug',
        'content',
        'status',
        'location_id'
    ];

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                return session('username') . ' telah menambah data.';
                break;

            case 'updated':
                return session('username') . ' telah mengubah data.';
                break;

            case 'deleted':
                return session('username') . ' telah menghapus data.';
                break;
        }
    }

    public function status()
    {
        if ($this->status == 1) {
            $status = '<span class="badge bg-danger"><i class="la la-times"></i></span>';
        } else if ($this->status == 2) {
            $status = '<span class="badge bg-success"><i class="la la-check"></i></span>';
        } else {
            $status = 'Invalid';
        }

        return $status;
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
