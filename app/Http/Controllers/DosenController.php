<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repository\{
	DosenRepository as Dosen,
	JurusanRepository as Jurusan,
};
use Validator;

class DosenController extends Controller
{
    /**
	 * controller api mengirim data dosen berdasarkan id
	 * 
	 * @param request
	 * @param id
	 * 
	 * @return App\Model\Dosen
	 * 
	 */
	function get(Request $request, $id){
		return parent::res( true,  Dosen::get($id));
	}

	/**
	 * mereturn data admin berdasarkan nip
	 * 
	 * @param request
	 * @param nip
	 * 
	 * @return App\Model\Dosen
	 * 
	 */
	function getByNip(Request $request, $nip){
		return parent::res(true, null, "Kamu mengakses getByNIP {$nip}");
	}

	function getAll(Request $request){
		return parent::res( true, Dosen::getAll($request) );
	}

	function getAllByJurusan(Request $request, $id_jurusan){

		$request->request->add([ "id_jurusan" => $id_jurusan ]);

		$validator = Validator::make( $request->all(), [
			"id_jurusan" => [
				"required",
				function($attr, $value, $fail){
					if(!Jurusan::get($value)) $fail("Jurusan tidak ditemukan");
				}
			]
		 ]);
		
		if($validator->fails())
			return parent::res(false, null, null, $validator->errors());

		return parent::res( true, Dosen::getAllByJurusan($request, Jurusan::get($id_jurusan)) );
	}
}
