<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CollectionEdition extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $connection = 'mysql';
    protected $table      = 'collections';
    protected $primaryKey = 'id';
    protected $fillable   = ['*'];

    protected static function boot()
    {
        parent::boot();

        // Define a default condition
        static::addGlobalScope('parent_id', function ($builder) {
            $builder->whereNotNull('parent_id')->where('parent_id', '!=', '0');
        });
    }

    public function collection()
    {
        return $this->belongsTo('App\Models\Collection', 'parent_id');
    }

    public function depositHead()
    {
        return $this->belongsTo('App\Models\DepositHead', 'deposit_head_id');
    }

    public function collectionCopy()
    {
        return $this->hasMany('App\Models\CollectionCopy', 'collection_id');
    }
}
