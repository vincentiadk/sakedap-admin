<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'admins';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'fullname',
        'address'
    ];

    public function user()
    {
        return $this->morphOne('App\Models\User', 'userable');
    }

    public function authClient()
    {
        return $this->morphOne('App\Models\AuthClient', 'authable');
    }
}
