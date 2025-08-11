<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contributor extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'contributors';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'name',
        'slug',
        'type',
        'is_creator',
        'show'
    ];

    public function type()
    {
        // if($this->type == 1) {
        //     $type = 'Buku';
        // } else if($this->type == 2) {
        //     $type = 'Partitur';
        // } else if($this->type == 3) {
        //     $type = 'Peta';
        // } else if($this->type == 4) {
        //     $type = 'Serial';
        // } else if($this->type == 5) {
        //     $type = 'Audio';
        // } else if($this->type == 6) {
        //     $type = 'Film';
        // } else {
        //     $type = 'Invalid';
        // }

        // return $type;

        $type = DepositHead::select('shape')->where('id', $this->type)->first()->shape;
        return isset($type) ? $type : 'invalid';
    }

    public function show()
    {
        if ($this->show == 0) {
            $show = "Hide";
        } else if ($this->show == 1) {
            $show = "Show";
        } else {
            $show = 'Invalid';
        }
        return $show;
    }

    public function collectionContributor()
    {
        return $this->hasMany('App\Models\CollectionContributor');
    }
}
