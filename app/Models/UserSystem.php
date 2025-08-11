<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'user_systems';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'name'
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User');
    }
}
