<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'user_accesses';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'role_id',
        'menu_id'
    ];

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function menu()
    {
        return $this->belongsTo('App\Models\Menu');
    }
}
