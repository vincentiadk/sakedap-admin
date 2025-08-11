<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryLocation extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'library_locations';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'library_id',
        'name',
        'publish'
    ];

    public function library()
    {
        return $this->belongsTo('App\Models\Library');
    }
}
