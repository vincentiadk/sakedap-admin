<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'tutorial';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'sequence',
        'title',
        'category',
        'content',
        'slug',
        'publish'
    ];
}
