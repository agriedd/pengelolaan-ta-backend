<?php

namespace App\Repository;

use App\Model\{
	Jurusan,
};

use Carbon\Carbon;

class JurusanRepository extends Repository
{

	public static function model(){
		return new Jurusan;
	}

	public static function get($id){
		return self::model()->info()->find($id);
	}

	public static function getByKdJurusan($kd_jurusan){
		return self::model()->info()->where("kd_jurusan", $kd_jurusan)->first();
	}

	public static function getByKdJurusanExcept($kd_jurusan, $id){
		return self::model()->info()->where("kd_jurusan", $kd_jurusan)->where("jurusan.id", "<>", $id)->first();
	}

	public static function getAll($request){
		return self::model()->info()->paginate($request->has("limit") ? $request->limit : 10);
	}

	public static function remove($id){
		return self::delete($id);
	}

	public static function delete($id){
		return self::model()->find($id)->delete();
	}

	public static function insert($request){
		$request
			->put("created_at", Carbon::now())
			->put("updated_at", Carbon::now());
		return self::model()->insertGetId($request->all());
	}

	public static function update($request, $id){
		$request->put("updated_at", Carbon::now());
		return self::model()->find($id)->update($request->all());
	}
}
