<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionRequest extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'collection_requests';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'collection_id',
        'request_letter',
        'count_download',
        'token_download',
        'approved_by',
        'status',
        'expired_at',
        'location_id',
    ];

    public function status()
    {
        if ($this->status == 1) {
            $status = 'Review';
        } else if ($this->status == 2) {
            $status = 'Diterima ';
        } else if ($this->status == 3) {
            $status = 'Ditolak';
        } else {
            $status = 'Invalid';
        }

        return $status;
    }

    public function getLinkDownload()
    {
        return url('download/original/' . $this->collection_id . '/?token=' . $this->token_download);
    }

    public function collection()
    {
        return $this->belongsTo('App\Models\Collection');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
