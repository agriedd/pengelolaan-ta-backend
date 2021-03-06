<?php

namespace App\Repository;

use App\Model\{
	InformasiDosen,
	Dosen,
};
use Carbon\Carbon;
use DB;

class InformasiDosenRepository extends Repository
{
	static function model(){
		return new InformasiDosen;
	}

    /**
     * menambah sebuah data informasidosen baru
     * 
     * @param Collection $collection
     * @param Model\Dosen $dosen
     * 
     * @return Model\InformasiDosen
     * 
     */
    static function insert($collection, Dosen $dosen){
    	$collection
    		->put("created_at", Carbon::now() )
    		->put("updated_at", Carbon::now() );
    	return $dosen->informasi()->create( $collection->all() );
    }

    public static function getLatestDosenQuery(){
        return self::model()->groupBy(DB::raw("id_dosen DESC"));;
    }
}
