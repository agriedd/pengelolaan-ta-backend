<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InformasiDosen extends Model
{
	protected $table = "informasi_dosen";
	protected $guarded = [];

	function dosen(){
		return $this->belongsTo(Dosen::class, "id_dosen", "id");
	}
}
