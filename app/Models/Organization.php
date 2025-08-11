<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'organizations';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'name'
    ];

    public function publisher()
    {
        return $this->hasMany('App\Models\Publisher');
    }
}
