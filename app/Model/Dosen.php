<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Repository\InformasiDosenRepository as InformasiDosenRepo;

class Dosen extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

	protected $table = "dosen";
	protected $guarded = [];

	protected $hidden = ["password"];


	/**
	 * Relasi hasMany dengan Informasi Dosen
	 * 
	 * @return QueryBuilder
	 */
	function informasi(){
		return $this->hasMany(InformasiDosen::class, "id_dosen", "id");
	}


	/**
	 * Relasi belongsTo dengan Prodi
	 * 
	 * @return QueryBuilder
	 * 
	 */
	function prodi(){
		return $this->belongsTo(Prodi::class, "id_prodi", "id");
	}
	

	/**
	 * relasi dengan riwayat secara morph
	 * 
	 * @return QueryBuilder
	 */
    public function riwayat(){
        return $this->morphMany(RiwayatLogin::class, 'user');
    }


    /**
     * relasi morph 1 - 1 dengan user, user akan memiliki
     * relasi dengan riwayat login terakhir miliknya
     * 
     * @return Query
     */
    public function riwayatTerakhir(){
        return $this->morphOne(RiwayatLogin::class, 'user')
            ->lastActive()
            ->success()
            ->limit(1);
    }


    /**
     * method info digunakan untuk melakukan join dengan informasi
     * dosen
     * 
     * @param QueryBuilder $query
     * 
     * @return QueryBuilder
     */
	function scopeInfo($query){
		$subquery = InformasiDosenRepo::getLatestDosenQuery();
		/*
		 | sub query terdapat pada App\Repository\InformasiDosenRepository
		 |
		 */
    	return $query
    		->select([ "informasi.*", "dosen.*" ])
    		->leftJoinSub($subquery, "informasi", function($join){
    			return $join->on("dosen.id", "=", "informasi.id_dosen");
    		});
	}
}
