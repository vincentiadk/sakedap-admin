<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'kunjungan';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'name',
    ];
}
