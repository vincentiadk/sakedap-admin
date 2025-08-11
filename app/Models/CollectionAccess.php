<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionAccess extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'collection_access';
    protected $fillable   = [
        'pemustaka_id',
        'collection_id',
        'tanggal_akses'
    ];
}
