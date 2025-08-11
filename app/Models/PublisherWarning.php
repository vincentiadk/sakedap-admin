<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublisherWarning extends Model
{
    use SoftDeletes;

    protected $fillable   = [
        'publisher_id',
        'warning_date',
        'library_id',
        'warning',
        'attachment',
        'reason',
        'location_id',
    ];

    public function publisher()
    {
        return $this->belongsTo('App\Models\Publisher');
    }

    public function library()
    {
        return $this->belongsTo('App\Models\Library');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location');
    }
}
