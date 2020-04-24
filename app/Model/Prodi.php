<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Repository\{
	InformasiProdiRepository as InformasiProdiRepository
};

class Prodi extends Model
{
	protected $table = "prodi";
	protected $guarded = [];

	/**
	 * relasi hanMany dengan InformasiProdi
	 * 
	 * @return QueryBuilder
	 * 
	 */
	function informasi(){
		return $this->hasMany(InformasiProdi::class, "id_prodi", "id");
	}

	/**
	 * relasi milik jurusan
	 * 
	 * @return QueryBuilder
	 * 
	 */
	function jurusan(){
		return $this->belongsTo(Jurusan::class, "id_jurusan", "id");
	}

	/**
	 * relasi hasMany Dosen
	 * 
	 * @return QueryBuilder
	 * 
	 */
	function dosen(){
		return $this->hasMany(Dosen::class, "id_prodi", "id");
	}

	/**
	 * mutasi query untuk left Join informasi prodi
	 * 
	 * @return QueryBuilder
	 * 
	 */
    public function scopeInfo($query){
    	$subquery = InformasiProdiRepository::getLatestProdiQuery();
    	return $query
    		->select([ "informasi.*", "prodi.*" ])
    		->leftJoinSub($subquery, "informasi", function($join){
    			return $join->on("prodi.id", "=", "informasi.id_prodi");
    		});
    }
}
