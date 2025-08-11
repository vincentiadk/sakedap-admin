<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCertainAccess extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'user_certain_accesses';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'role_id',
        'access'
    ];

    public function access()
    {
        switch ($this->access) {
            case '1':
                $access = 'Hapus Koleksi';
                break;
            case '2':
                $access = 'Kunci Koleksi';
                break;
            case '3':
                $access = 'Melihat Kinerja Semua User';
                break;
            default:
                $access = 'Invalid';
                break;
        }

        return $access;
    }
}
