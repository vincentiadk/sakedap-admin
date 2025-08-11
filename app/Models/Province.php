<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'provinces';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'id',
        'name',
        'latitude',
        'longitude',
        'code'
    ];

    public function city()
    {
        return $this->hasMany('App\Models\City');
    }
}
