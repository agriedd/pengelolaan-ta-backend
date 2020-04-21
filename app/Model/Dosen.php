<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
	protected $table = "dosen";
	protected $guarded = [];

	protected $hidden = ["password"];
}
