<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionContributor extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'collection_contributors';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'collection_id',
        'contributor_id',
        'author_id'
    ];

    public function collection()
    {
        return $this->belongsTo('App\Models\Collection');
    }

    public function contributor()
    {
        return $this->belongsTo('App\Models\Contributor');
    }

    public function author()
    {
        return $this->belongsTo('App\Models\Author');
    }
}
