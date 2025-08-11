<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Problem extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'problems';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'name'
    ];

    public function collectionProblem()
    {
        return $this->hasMany('App\Models\CollectionProblem');
    }
}
