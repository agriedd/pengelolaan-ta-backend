<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RiwayatLogin extends Model
{
	protected $table = "riwayat_login";
	protected $guarded = [];


	//mutasi

	function userable()
	{
		return $this->morphTo();
	}

}
