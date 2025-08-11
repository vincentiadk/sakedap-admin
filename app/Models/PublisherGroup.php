<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublisherGroup extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'publisher_groups';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'name',
    ];

    public function groups()
    {
        return $this->hasMany('App\Models\PublisherAccess');
    }
}
