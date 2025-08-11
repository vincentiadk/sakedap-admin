<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $connection = 'inlis';
    protected $table      = 'members';
    protected $primaryKey = 'id';
}
