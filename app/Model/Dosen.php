<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
	protected $table = "dosen";
	protected $guarded = [];

	protected $hidden = ["password"];

	function informasi(){
		return $this->hasMany(InformasiDosen::class, "id_dosen", "id");
	}
}
