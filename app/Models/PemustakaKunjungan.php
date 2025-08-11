<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemustakaKunjungan extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'pemustaka_kunjungan';
    protected $primaryKey = 'id';

    protected $fillable = ['pemustaka_id', 'kunjungan_id'];
}
