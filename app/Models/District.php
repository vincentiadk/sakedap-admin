<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'districts';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'city_id',
        'name',
        'latitude',
        'longitude'
    ];

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function village()
    {
        return $this->hasMany('App\Models\Village');
    }
}
