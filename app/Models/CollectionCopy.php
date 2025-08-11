<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CollectionCopy extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $connection = 'mysql';
    protected $table      = 'collection_copies';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'collection_id',
        'lib_loc_id',
        'condition',
        'delivery_form_id',
        'availability',
        'edit_by',
        'created_by',
        'deleted_at',
        'received_at',
        'received_by',
        'code'
    ];

    public function availability_list()
    {
        return [
            '0' => 'tersedia',
            '1' => 'dalam pengiriman ke pengelolaan',
            '2' => 'sedang didayagunakan',
            '3' => 'hilang',
            '4' => 'rusak',
            '5' => 'sedang diperbaiki',
            '6' => 'sedang diolah',
            '7' => 'masih di ekspedisi',
            '8' => 'sedang dicek',
            '9' => 'diterima pengolahan',
            '10' => 'diterima tim kckr',
            '11' => 'ditolak',
        ];
    }

    public function condition_list()
    {
        return [
            '1' => 'Sangat Baik',
            '2' => 'Baik',
            '3' => 'Cukup',
            '4' => 'Rusak'
        ];
    }

    public function availability_text()
    {
        $list = $this->availability_list();
        if (!empty($this->availability)) {
            if (isset($list[$this->availability])) {
                return $list[$this->availability];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function condition_text()
    {
        $list = $this->condition_list();
        if (!empty($this->condition)) {
            if (isset($list[$this->condition])) {
                return $list[$this->condition];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function collection()
    {
        return $this->belongsTo('App\Models\Collection', 'collection_id');
    }

    public function edition()
    {
        return $this->belongsTo('App\Models\CollectionEdition', 'collection_id');
    }

    public function lib_location()
    {
        return $this->belongsTo('App\Models\LibraryLocation', 'lib_loc_id');
    }

    public function delivery_form()
    {
        return $this->belongsTo('App\Models\DeliveryForm', 'delivery_form_id');
    }

    public function copy_delivery()
    {
        return $this->hasMany('App\Models\CopyDelivery', 'collection_copy_id');
    }

    public function copy_rejected()
    {
        return $this->hasOne('App\Models\CopyRejected', 'collection_copy_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo('App\Models\User', 'received_by');
    }
}
