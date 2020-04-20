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


	public function informasi(){
		return $this->hasMany(InformasiJurusan::class, "id_jurusan", "id");
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
