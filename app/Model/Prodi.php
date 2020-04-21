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


	function informasi(){
		return $this->hasMany(InformasiProdi::class, "id_prodi", "id");
	}
	function jurusan(){
		return $this->belongsTo(Jurusan::class, "id_jurusan", "id");
	}
	function dosen($prodi){
		return $this->hasMany(Dosen::class, "id_prodi", "id");
	}

    public function scopeInfo($query){
    	$subquery = InformasiProdiRepository::getLatestProdiQuery();
    	return $query
    		->select([ "informasi.*", "prodi.*" ])
    		->leftJoinSub($subquery, "informasi", function($join){
    			return $join->on("prodi.id", "=", "informasi.id_prodi");
    		});
    }
}
