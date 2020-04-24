<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\{
	Repository\InformasiJurusanRepository as InfoJurusanRepo,
};

class Jurusan extends Model
{
	protected $table = "jurusan";
	protected $guarded = [];

	/**
	 * relasi hasMany dengan InformasiJurusan
	 * 
	 * @return QueryBuilder
	 * 
	 */
	public function informasi(){
		return $this->hasMany(InformasiJurusan::class, "id_jurusan", "id");
	}

	/**
	 * relasi hasManu dengan Prodi
	 * 
	 * @return QueryBuilder
	 * 
	 */
	public function prodi(){
		return $this->hasMany(Prodi::class, "id_jurusan", "id");
	}

	public function dosen(){
		return $this->hasManyThrough(Dosen::class, Prodi::class, "id_jurusan", "id_prodi", "id", "id");
	}

	/**
	 * relasi has Many dengan Admin
	 * 
	 * @return QueryBuilder
	 * 
	 */
	public function admin(){
		return $this->hasMany(Admin::class, "id_jurusan", "id");
	}

	/**
	 * scope untuk melakukan join dengan tabel informasiJurusan
	 * 
	 * @param QueryBuilder query
	 * 
	 * @return QueryBuilder
	 * 
	 */
	public function scopeInfo($query)
	{
		$subquery = InfoJurusanRepo::getLatestInfoJurusanQuery();
    	return $query
    		->select([ "informasi.*", "jurusan.*" ])
    		->leftJoinSub($subquery, "informasi", function($join){
    			return $join->on("jurusan.id", "=", "informasi.id_jurusan");
    		});
	}
}
