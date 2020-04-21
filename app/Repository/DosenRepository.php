<?php

namespace App\Repository;

use App\Model\{
	Dosen,
};

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

	static function getAll($request){
		return self::model()->paginate($request->has("limit") ? $request->limit : 10);
	}
}
