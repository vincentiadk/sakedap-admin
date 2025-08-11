<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CopyDelivery extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $connection = 'mysql';
    protected $table      = 'copy_delivery_internals';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'delivery_internal_date',
        'accepted_date',
        'created_at',
        'updated_at',
        'deleted_at',
        'system_id',
        'collection_copy_id',
        'user_delivery_id',
        'updated_by',
        'created_by'
    ];

    public function copy()
    {
        return $this->belongsTo('App\Models\CollectionCopy', 'collection_copy_id');
    }

    public function user_delivery()
    {
        return $this->belongsTo('App\Models\User', 'user_delivery_id');
    }
}
