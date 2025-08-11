<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthClient extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'auth_clients';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'client_id',
        'client_secret',
        'authable_id',
        'authable_type',
    ];

    public function authable()
    {
        return $this->morphTo();
    }

    public function publisherAccess()
    {
        return $this->belongsTo('App\Models\PublisherAccess', 'authable_id', 'publisher_id');
    }
}
