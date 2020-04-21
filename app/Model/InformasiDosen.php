<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InformasiDosen extends Model
{
	protected $table = "informasi_dosen";
	protected $guarded = [];


	/**
	 * relasi belongsTo terhadap Dosen
	 * 
	 * @return QueryBuilder
	 * 
	 */
	function dosen(){
		return $this->belongsTo(Dosen::class, "id_dosen", "id");
	}
}
