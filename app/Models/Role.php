<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'roles';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'name'
    ];

    public function user()
    {
        return $this->hasMany('App\Models\User');
    }

    public function userAccess()
    {
        return $this->hasMany('App\Models\UserAccess');
    }
}
