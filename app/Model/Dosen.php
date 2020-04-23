<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Repository\InformasiDosenRepository as InformasiDosenRepo;

class Dosen extends Model
{
	protected $table = "dosen";
	protected $guarded = [];

	protected $hidden = ["password"];


	/**
	 * Relasi hasMany dengan Informasi Dosen
	 * 
	 * @return QueryBuilder
	 * 
	 */
	function informasi(){
		return $this->hasMany(InformasiDosen::class, "id_dosen", "id");
	}

	function scopeInfo($query){
		$subquery = InformasiDosenRepo::getLatestDosenQuery();
    	return $query
    		->select([ "informasi.*", "dosen.*" ])
    		->leftJoinSub($subquery, "informasi", function($join){
    			return $join->on("dosen.id", "=", "informasi.id_dosen");
    		});
	}
}
