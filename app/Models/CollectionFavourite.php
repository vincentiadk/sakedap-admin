<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionFavourite extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'collection_favourites';
    protected $fillable   = [
        'pemustaka_id',
        'collection_id',
        'tanggal_favorit'
    ];
}
