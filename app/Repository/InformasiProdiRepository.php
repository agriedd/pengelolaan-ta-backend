<?php

namespace App\Repository;
use App\Model\{
	InformasiProdi,
	Prodi,
};
use DB;
use Carbon\Carbon;

class InformasiProdiRepository extends Repository
{
	public static function model()
	{
		return new InformasiProdi;
	}

	public static function insert($collection, Prodi $prodi){
		$collection
			->put("created_at", Carbon::now())
			->put("updated_at", Carbon::now());
		return $prodi->informasi()->create($collection->all());
	}

	public static function getLatestProdiQuery(){
		return self::model()->groupBy(DB::raw("id_prodi DESC"));;
	}
}
