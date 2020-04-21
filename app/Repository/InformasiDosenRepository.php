<?php

namespace App\Repository;

use App\Model\{
	InformasiDosen,
	Dosen,
};
use Corbon\Carbon;

class InformasiDosenRepository extends Repository
{
	static function model(){
		return new InformasiDosen;
	}
    static function insert($collection, Dosen $dosen){
    	$collection
    		->put("created_at", Carbon::now() )
    		->put("updated_at", Carbon::now() );
    	return $dosen->informasi()->create( $collection->all() );
    }
}
