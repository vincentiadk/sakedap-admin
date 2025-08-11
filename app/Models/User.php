<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserAccess;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{

    use SoftDeletes, Notifiable, HasApiTokens;

    protected $connection = 'mysql';
    protected $table      = 'users';
    protected $dates      = ['deleted_at'];
    protected $primaryKey = 'id';
    protected $fillable   = [
        'userable_type',
        'userable_id',
        'library_id',
        'role_id',
        'username',
        'email',
        'password',
        'lang',
        'last_login',
        'enable',
        'verification_at',
        'user_agent_login'
    ];

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function library()
    {
        return $this->belongsTo('App\Models\Library');
    }

    public function admin()
    {
        return $this->hasOne('App\Models\Admin', 'id', 'userable_id');
    }

    public function publisher()
    {
        return $this->hasOne('App\Models\Publisher', 'id', 'userable_id');
    }

    public function userSystem()
    {
        return $this->hasOne('App\Models\UserSystem', 'id', 'userable_id');
    }

    public function hasAccess($role_id, $menu_id)
    {
        if (UserAccess::where('role_id', $role_id)->where('menu_id', $menu_id)->count() > 0) {
            return false;
        } else {
            return true;
        }
    }
}
