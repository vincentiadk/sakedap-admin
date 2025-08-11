<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'cities';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'province_id',
        'name',
        'latitude',
        'longitude',
        'code'
    ];

    public function province()
    {
        return $this->belongsTo('App\Models\Province');
    }

    public function district()
    {
        return $this->hasMany('App\Models\District');
    }

    public function collection()
    {
        return $this->hasMany('App\Models\Collection');
    }
}
