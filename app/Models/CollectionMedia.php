<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helper\GeneralHelper;
use Illuminate\Support\Facades\Storage;
use App\Models\Location;

class CollectionMedia extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'collection_media';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'collection_id',
        'link',
        'size',
        'extension',
        'mimes',
        'hash',
        'type',
        'method',
        'created_at',
        'updated_at',
        'location_id',
    ];

    public function collection()
    {
        return $this->belongsTo('App\Models\Collection');
    }

    public function method()
    {
        if ($this->method == 1) {
            $method = 'API';
        } else if ($this->method == 2) {
            $method = 'SFTP';
        } else if ($this->method == 3) {
            $method = 'Mandiri';
        } else if ($this->method == 4) {
            $method = 'Manual';
        } else if ($this->method == 5) {
            $method = 'Sistem';
        } else if ($this->method == 6) {
            $method = 'Bulk Penerbit';
        } else if ($this->method == 7) {
            $method = 'Bulk Admin';
        } else {
            $method = 'Invalid';
        }

        return $method;
    }

    public function type()
    {
        if ($this->type == 1) {
            $type = 'Cover';
        } else if ($this->type == 2) {
            $type = 'Pdf Original';
        } else if ($this->type == 3) {
            $type = 'Pdf Watermark';
        } else if ($this->type == 4) {
            $type = 'Audio Original';
        } else if ($this->type == 5) {
            $type = 'Audio Preview';
        } else if ($this->type == 6) {
            $type = 'Audio Watermark';
        } else if ($this->type == 7) {
            $type = 'Video Original';
        } else if ($this->type == 8) {
            $type = 'Video Preview';
        } else if ($this->type == 9) {
            $type = 'Video Watermark';
        } else {
            $type = 'Invalid';
        }

        return $type;
    }

    public function getImageBook()
    {

        if ($this->collection->type == 1) {
            $type = 'book';
        } else if ($this->collection->type == 2) {
            $type = 'partitur';
        } else if ($this->collection->type == 3) {
            $type = 'map';
        } else if ($this->collection->type == 4) {
            $type = 'serial';
        } else if ($this->collection->type == 5) {
            $type = 'audio';
        } else if ($this->collection->type == 6) {
            $type = 'film';
        } else {
            $type = 'Invalid';
        }

        $location   = Location::find($this->location_id)->location;
        $files      = Storage::disk($location)->files('public/collection/' . $type . '/images/' . $this->collection_id);
        $jumlahFile = count($files);
        $result     = [];
        $arr        = explode("/", $this->link);
        $namaFile   = end($arr);

        for ($i = 1; $i <= $jumlahFile; $i++) {
            $result[] = $this->link . "-$i.jpg";
        }

        return $result;
    }

    public function jsonParse()
    {
        if ($this->collection->type == 1) {
            $type = 'book';
        } else if ($this->collection->type == 2) {
            $type = 'partitur';
        } else if ($this->collection->type == 3) {
            $type = 'map';
        } else if ($this->collection->type == 4) {
            $type = 'serial';
        } else if ($this->collection->type == 5) {
            $type = 'audio';
        } else if ($this->collection->type == 6) {
            $type = 'film';
        } else {
            $type = 'Invalid';
        }

        $location = Location::find($this->location_id)->location;
        if ($type == 'serial') {
            $files = Storage::disk($location)->files('public/collection/' . $type . '/edition/images/' . $this->collection_id);
        } else {
            $files = Storage::disk($location)->files('public/collection/' . $type . '/images/' . $this->collection_id);
        }

        $jumlahFile = count($files);
        $result = [];
        $arr = explode("/", $this->link);
        $namaFile = end($arr);

        for ($i = 1; $i <= $jumlahFile; $i++) {
            $item     = GeneralHelper::encryptString($this->link . "-$i.jpg");
            $result[] = url('collection/images/' . $item) . "?storage=" . $location;
        }

        return $result;
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
