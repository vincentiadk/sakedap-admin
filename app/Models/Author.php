<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'authors';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'fullname',
        'title',
        'slug',
        'year_of_birth',
        'year_of_death',
        'photo',
        'count'
    ];

    public function collectionContributor()
    {
        return $this->hasMany('App\Models\CollectionContributor');
    }
}
