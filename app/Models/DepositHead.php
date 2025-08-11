<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositHead extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'deposit_head';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'shape',
        'code',
        'category'
    ];

    public function icon()
    {
        if ($this->id == 1) {
            $icon = 'la la-book';
        } else if ($this->id == 2) {
            $icon = 'la la-file-audio-o';
        } else if ($this->id == 3) {
            $icon = 'la la-map';
        } else if ($this->id == 4) {
            $icon = 'la la-list-alt';
        } else if ($this->id == 5) {
            $icon = 'la la-music';
        } else if ($this->id == 6) {
            $icon = 'la la-film';
        } else if ($this->id == 7) {
            $icon = 'la la-book';
        } else if ($this->id == 8) {
            $icon = 'la la-institution';
        } else if ($this->id == 9) {
            $icon = 'la la-globe';
        } else if ($this->id == 10) {
            $icon = 'la la-file-text';
        } else if ($this->id == 11) {
            $icon = 'la la-newspaper-o';
        } else if ($this->id == 12) {
            $icon = 'la la-map';
        } else if ($this->id == 13) {
            $icon = 'la la-music';
        } else if ($this->id == 14) {
            $icon = 'la la-video-camera';
        } else if ($this->id == 15) {
            $icon = 'la la-film';
        } else {
            $icon = 'Invalid';
        }

        return $icon;
    }
}
