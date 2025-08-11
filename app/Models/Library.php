<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Library extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'libraries';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'province_id',
        'name',
        'address',
        'api_token'
    ];

    public function province()
    {
        return $this->belongsTo('App\Models\Province');
    }
}
