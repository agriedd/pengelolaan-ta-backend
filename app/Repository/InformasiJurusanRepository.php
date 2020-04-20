<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Model;
use App\Model\{
	Jurusan,
	InformasiJurusan,
};
use Carbon\Carbon;
use DB;

class InformasiJurusanRepository extends Model
{
    
	public static function model(){
		return new InformasiJurusan;
	}

	public static function insert($request, Jurusan $jurusan){
		$request
			->put("created_at", Carbon::now())
			->put("updated_at", Carbon::now());

		return $jurusan->informasi()->create( $request->all() );
	}

	/**
	 * subquery untuk join menggunakan joinSub()
	 * 
	 * @return QueryBuilder
	 * 
	 */
	public static function getLatestInfoJurusanQuery(){
		return self::model()->groupBy(DB::raw("id_jurusan DESC"));
	}
}
