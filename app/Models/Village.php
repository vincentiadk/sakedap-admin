<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Village extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'villages';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'id',
        'district_id',
        'name',
        'latitude',
        'longitude'
    ];

    public function district()
    {
        return $this->belongsTo('App\Models\District');
    }
}
