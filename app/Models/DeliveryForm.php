<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DeliveryForm extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'delivery_form';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'expedition_id',
        'delivery_date',
        'publisher_id',
        'receipt_no',
        'library_id',
        'status',
        'letter_no',
        'accepted_by',
        'accepted_date'
    ];


    public function expedition()
    {
        return $this->belongsTo('App\Models\Expedition');
    }

    public function library()
    {
        return $this->belongsTo('App\Models\Library');
    }

    public function publisher()
    {
        return $this->belongsTo('App\Models\Publisher');
    }

    public function collectionCopy()
    {
        return $this->hasMany('App\Models\CollectionCopy');
    }

    public function collectionCopyDistinct()
    {
        return $this->hasMany('App\Models\CollectionCopy')->groupBy('collection_id');
    }
}
