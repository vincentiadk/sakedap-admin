<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'collections';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $appends    = ['icon'];
    protected $fillable   = [
        'id_old',
        'publisher_id',
        'city_id',
        'parent_id',
        'title',
        'title_ori',
        'physical_description',
        'album',
        'slug',
        'type',
        'type_book',
        'series',
        'edition',
        'deposit_head_id',
        'mark_province',
        'mark_national',
        'price',
        'serial',
        'ddc',
        'volume',
        'deposit',
        'code',
        'code_type',
        'code_kdt',
        'source',
        'publication_month',
        'publication_year',
        'copyright',
        'preview',
        'description',
        'problem',
        'sync',
        'lock',
        'manual',
        'date',
        'access',
        'status',
        'manage_by',
        'rejected_at',
        'rejected_by',
        'received_at',
        'received_by',
        'edit_by',
        'created_by',
        'updated_by',
        'validated_by',
        'validated_at',
        'currency',
        'start_publication_date',
        'end_publication_date',
    ];

    public function physicalDescription()
    {
        return json_decode($this->physical_description);
    }

    public function parent()
    {
        return Collection::where('id', $this->parent_id)->first();
    }

    public function edition()
    {
        return Collection::where('parent_id', $this->id);
    }

    public function publisher()
    {
        return $this->belongsTo('App\Models\Publisher');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function serial()
    {
        if ($this->serial == 1) {
            $serial = 'Harian';
        } else if ($this->serial == 2) {
            $serial = 'Mingguan';
        } else if ($this->serial == 3) {
            $serial = 'Bulanan';
        } else if ($this->serial == 4) {
            $serial = '3 Bulan Sekali';
        } else if ($this->serial == 5) {
            $serial = '4 Bulan Sekali';
        } else if ($this->serial == 6) {
            $serial = '6 Bulan Sekali';
        } else if ($this->serial == 7) {
            $serial = 'Tahunan';
        } else if ($this->serial == 8) {
            $serial = '2 Tahun Sekali';
        } else if ($this->serial == 9) {
            $serial = '3 Tahun Sekali';
        } else {
            $serial = $this->serial;
        }

        return $serial;
    }

    public function publicationMonth()
    {
        if ($this->publication_month == '01') {
            $publication_month = 'Januari';
        } else if ($this->publication_month == '02') {
            $publication_month = 'Februari';
        } else if ($this->publication_month == '03') {
            $publication_month = 'Maret';
        } else if ($this->publication_month == '04') {
            $publication_month = 'April';
        } else if ($this->publication_month == '05') {
            $publication_month = 'Mei';
        } else if ($this->publication_month == '06') {
            $publication_month = 'Juni';
        } else if ($this->publication_month == '07') {
            $publication_month = 'Juli';
        } else if ($this->publication_month == '08') {
            $publication_month = 'Agustus';
        } else if ($this->publication_month == '09') {
            $publication_month = 'September';
        } else if ($this->publication_month == '10') {
            $publication_month = 'Oktober';
        } else if ($this->publication_month == '11') {
            $publication_month = 'November';
        } else if ($this->publication_month == '12') {
            $publication_month = 'Desember';
        } else {
            $publication_month = 'Invalid';
        }

        return $publication_month;
    }

    public function getIconAttribute()
    {
        if ($this->type == 1) {
            $icon = 'la la-book';
        } else if ($this->type == 2) {
            $icon = 'la la-file-audio-o';
        } else if ($this->type == 3) {
            $icon = 'la la-map';
        } else if ($this->type == 4) {
            $icon = 'la la-list-alt';
        } else if ($this->type == 5) {
            $icon = 'la la-music';
        } else if ($this->type == 6) {
            $icon = 'la la-film';
        } else {
            $icon = 'Invalid';
        }

        return $icon;
    }

    public function type()
    {
        if ($this->type == 1) {
            $type = 'book';
        } else if ($this->type == 2) {
            $type = 'partitur';
        } else if ($this->type == 3) {
            $type = 'map';
        } else if ($this->type == 4) {
            $type = 'serial';
        } else if ($this->type == 5) {
            $type = 'audio';
        } else if ($this->type == 6) {
            $type = 'film';
        } else {
            $type = 'Invalid';
        }

        return $type;
    }

    public function typeBook()
    {
        if ($this->type_book == 1) {
            $typeBook = 'Elektronik';
        } else if ($this->type_book == 2) {
            $typeBook = 'Cetak';
        } else {
            $typeBook = 'Invalid';
        }

        return $typeBook;
    }

    public function codeType()
    {
        if ($this->code_type == 1) {
            $codeType = 'Isbn';
        } else if ($this->code_type == 2) {
            $codeType = 'Ismn';
        } else if ($this->code_type == 3) {
            $codeType = 'Isrc';
        } else if ($this->code_type == 4) {
            $codeType = 'Issn';
        } else if ($this->code_type == 5) {
            $codeType = 'Isan';
        } else {
            $codeType = 'Invalid';
        }

        return $codeType;
    }

    public function status()
    {
        if ($this->status == 1) {
            $status = 'Review';
        } else if ($this->status == 2) {
            $status = 'Diterima ';
        } else if ($this->status == 3) {
            $status = 'Masalah';
        } else if ($this->status == 4) {
            $status = 'Pre Proses';
        } else if ($this->status == 5) {
            $status = 'Ditolak';
        } else {
            $status = 'Invalid';
        }

        return $status;
    }

    public function access()
    {
        if ($this->access == 1) {
            $status = 'Akses full file berwatermak secara online';
        } else if ($this->access == 2) {
            $status = 'Akses hanya preview file secara online, namun tetap dapat di dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan internet LAN';
        } else if ($this->access == 3) {
            $status = 'Akses hanya file preview secara online, dan tidak didayagunakan di lingkungan Perpustakaan Nasional RI selama 5 tahun sejak diserahkan. Setelah 5 tahun, akan didayagunakan oleh Perpustakaan Nasional RI di jaringan internet LAN';
        } else if ($this->access == 4) {
            $status = 'Akses hanya file preview secara online selamanya dan tidak didayagunakan di mana pun';
        } else {
            $status = 'Invalid';
        }

        return $status;
    }

    public function getFirstPreviewPage()
    {
        $page = 1;
        if ($this->acccess != 1) {
            if ($this->type == 4) {

                if (isset($this->preview)) {
                    $preview = explode('-', $this->preview);
                    $page = $preview[0];
                } else {
                    if ($this->parent() != null && isset($this->parent()->preview)) {
                        $preview = explode('-', $this->parent()->preview);
                        $page = $preview[0];
                    } else {
                        $page = 1;
                    }
                }
            } else {
                $preview = explode('-', $this->preview);
                $page = $preview[0];
            }
        }

        return $page;
    }

    public function totalCopy($library_id = null)
    {
        if (empty($library_id)) {
            return CollectionCopy::where('collection_id', $this->id)->count();
        } else {
            return CollectionCopy::where('collection_id', $this->id)
                ->whereHas('lib_location', function ($subquery) use ($library_id) {
                    $subquery->where('library_id', $library_id);
                })->count();
        }
    }

    public function manageBy()
    {
        return $this->belongsTo('App\Models\User', 'manage_by');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\Models\User', 'updated_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo('App\Models\User', 'received_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo('App\Models\User', 'received_by');
    }

    public function editBy()
    {
        return $this->belongsTo('App\Models\User', 'edit_by');
    }

    public function validatedBy()
    {
        return $this->belongsTo('App\Models\User', 'validated_by');
    }

    public function collectionProblem()
    {
        return $this->hasMany('App\Models\CollectionProblem');
    }

    public function collectionContributor()
    {
        return $this->hasMany('App\Models\CollectionContributor');
    }

    public function collectionMedia()
    {
        return $this->hasMany('App\Models\CollectionMedia');
    }

    public function collectionSubject()
    {
        return $this->hasMany('App\Models\CollectionSubject');
    }

    public function collectionCategory()
    {
        return $this->hasMany('App\Models\CollectionCategory');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\CollectionCategory');
    }

    public function subject()
    {
        return $this->belongsTo('App\Models\CollectionSubject');
    }

    public function collectionRequest()
    {
        return $this->hasMany('App\Models\CollectionRequest');
    }

    public function collectionEdition()
    {
        return Collection::where('parent_id', $this->id)->where('type', $this->type);
    }

    public function depositHead()
    {
        return $this->belongsTo('App\Models\DepositHead', 'deposit_head_id');
    }

    public function collectionCopy()
    {
        return $this->hasMany('App\Models\CollectionCopy', 'collection_id');
    }

    public function collectionEditionModel()
    {
        return $this->hasMany('App\Models\CollectionEdition', 'parent_id');
    }

    public function collectionEditionCopy()
    {
        return $this->hasManyThrough(
            'App\Models\CollectionCopy',
            'App\Models\CollectionEdition',
            'parent_id', // Foreign key on the edition table...
            'collection_id', // Foreign key on the copy table...
            'id', // Local key on the collections table...
            'id' // Local key on the edition table...
        );
    }
}
