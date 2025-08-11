<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesktopToken extends Model
{

	protected $connection = 'mysql';
	protected $fillable   = [
		'ip_address',
		'token',
		'enable',
		'expired_at'
	];
}
