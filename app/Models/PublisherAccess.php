<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublisherAccess extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'publisher_access';
    protected $fillable   = ['publisher_group_id', 'publisher_id', 'system_type', 'code_system'];

    public function publisher()
    {
        return $this->belongsTo('App\Models\Publisher');
    }

    public function publisherGroup()
    {
        return $this->belongsTo('App\Models\PublisherGroup');
    }

    public function authClient()
    {
        return $this->morphOne('App\Models\AuthClient', 'authable');
    }
}
