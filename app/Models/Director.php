<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Director extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'directors';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'signature',
        'nip',
        'name',
        'position',
        'position_start',
        'position_end',
        'location_id',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function signature()
    {
        $location = $this->location->location;
        if (Storage::disk($location)->exists($this->signature)) {
            $signature = asset(Storage::disk($location)->url($this->signature));
        } else {
            $signature = asset('main/default.png');
        }

        return $signature;
    }

    public function positionTime()
    {
        $get = Director::orderByRaw('DATE(position_start) DESC')
            ->limit(1)
            ->get();

        if ($get->count() > 0) {
            if ($get[0]->id == $this->id) {
                $position_time = 'Masih Menjabat';
            } else {
                $position_time = 'Jabatan Telah Berakhir';
            }
        } else {
            $position_time = 'Jabatan Telah Berakhir';
        }

        return $position_time;
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location');
    }
}
