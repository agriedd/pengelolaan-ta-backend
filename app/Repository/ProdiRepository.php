<?php

namespace App\Repository;

use App\Model\{
	Prodi,
	Jurusan,
};
use Carbon\Carbon;

class ProdiRepository extends Repository
{

	static function model(){
		return new Prodi; 
	}

	public static function insert($collection, Jurusan $jurusan){
		$collection
			->put("created_at", Carbon::now())
			->put("updated_at", Carbon::now());
		return $jurusan->prodi()->create( $collection->all() );
	}

	static function get($id)
	{
		return self::model()->info()->with(["jurusan"])->find($id);
	}
	static function getByKdProdi($kd_prodi)
	{
		return self::model()->info()->with(["jurusan"])->where("kd_prodi", $kd_prodi)->first();
	}
	static function getAll($request)
	{
		return self::model()->info()->with(["jurusan"])->paginate($request->has("limit") ? $request->limit : 10);
	}
}
