<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionProblem extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'collection_problems';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'collection_id',
        'problem_id',
        'solved'
    ];

    public function problem()
    {
        return $this->belongsTo('App\Models\Problem');
    }
}
