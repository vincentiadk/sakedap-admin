<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'downloads';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'user_id',
        'slug',
        'link',
        'description',
        'status',
        'location_id',
    ];

    public function slug()
    {
        if ($this->slug == 'bill_isbn') {
            $slug = 'Laporan Tagihan ISBN';
        } else if ($this->slug == 'publisher') {
            $slug = 'Laporan Penerbit';
        } else if ($this->slug == 'collection') {
            $slug = 'Laporan Koleksi';
        } else if ($this->slug == 'collection-kckra') {
            $slug = 'Laporan Koleksi KCKRA';
        } else if ($this->slug == 'metadata-isbn') {
            $slug = 'Metadata Bulk ISBN';
        } else if ($this->slug == 'performance_user') {
            $slug = 'Laporan Kinerja User';
        } else if ($this->slug == 'periodic') {
            $slug = 'Laporan Periodic';
        } else if ($this->slug == 'data_isrc') {
            $slug = 'Data ISRC';
        } else if ($this->slug == 'publisher_isbn') {
            $slug = 'Laporan Publisher ISBN';
        } else if ($this->slug == 'collection_delivery') {
            $slug = 'Laporan Pengiriman Koleksi';
        } else if ($this->slug == 'report_distribution') {
            $slug = 'Laporan Distribusi';
        } else {
            $slug = 'Invalid';
        }

        return $slug;
    }

    public function description()
    {
        return json_decode($this->description);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
