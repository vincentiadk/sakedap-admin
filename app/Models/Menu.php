<?php

namespace App\Models;

use App\Models\UserAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'menus';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'name',
        'icon',
        'url',
        'parent_id',
        'order'
    ];

    public function child()
    {
        return Menu::where('parent_id', $this->id)
            ->oldest('order')
            ->get();
    }

    public function checkPermission($role_id)
    {
        $check =  UserAccess::where('role_id', $role_id)
            ->where('menu_id', $this->id)
            ->count();

        if ($check > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getChildArr()
    {
        $get = Menu::where('parent_id', $this->id)->get();
        $arr = [];

        foreach ($get as $g) {
            $arr[] = $g->id;
        }

        return $arr;
    }

    public function parent()
    {
        return Menu::find($this->parent_id);
    }

    public function userAccess()
    {
        return $this->hasMany('App\Models\UserAccess');
    }
}
