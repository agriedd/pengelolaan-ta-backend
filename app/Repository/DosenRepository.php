<?php

namespace App\Repository;

use App\Model\{
	Dosen,
};
use Carbon\Carbon;

class DosenRepository extends Repository
{
	static function model(){
		return new Dosen();
	}


	/**
	 * mengambil data dosen berdasarkan id
	 * 
	 * @param int id
	 * 
	 * @return Model\Dosen
	 * 
	 */
	static function get($id){
		return self::model()->find($id);
	}

	/**
	 * mengambil data dosen berdasarkan nip
	 * 
	 * @param int nip
	 * 
	 * @return Model\Dosen
	 * 
	 * @todo cek lagi apakah mengambil nip dari informasi Dosen terakhir
	 * saja atau sepanjang waktu
	 * 
	 */
	static function getByNip($nip){
		return self::model()->where("informasi.nip", $nip)->first(); 
	}

	/**
	 * mengambil dosen dengan menggunakan username
	 * 
	 * @param string username
	 * 
	 * @return Model\Dosen
	 * 
	 */
	static function getByUsername($username){
		return self::model()->where("username", $username)->first(); 
	}

	/**
	 * mengambil semua data dosen
	 * 
	 * @param Request $request
	 * 
	 * @return Collection
	 * 
	 * @todo tambahkan filter
	 * 
	 */
	static function getAll($request){
		return self::model()->paginate($request->has("limit") ? $request->limit : 10);
	}


	/**
	 * menambah data sebuah dosen baru
	 * 
	 * @param Collection $collection
	 * 
	 * @return int id dari data dosen baru
	 * 
	 */
	static function insert($collection){
		$collection
			->put("created_at", Carbon::now())
			->put("updated_at", Carbon::now());
		return self::model()->insertGetId($collection->all());
	}
}
