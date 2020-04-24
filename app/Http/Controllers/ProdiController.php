<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\{
	ProdiRepository as Prodi,
	JurusanRepository as Jurusan,
};
use Validator;

class ProdiController extends Controller
{
	public function get(Request $request, $id)
	{
		return parent::res(true, Prodi::get($id));
	}
	public function getAll(Request $request)
	{
		return parent::res(true, Prodi::getAll($request));
	}
	public function getAllByJurusan(Request $request, $id_jurusan){

		$request->request->add([ "id_jurusan" => $id_jurusan ]);

		$validator = Validator::make( $request->all(), [
			"id_jurusan" => [
				"required",
				function($attr, $value, $fail){
					if(!Jurusan::get($value))
						$fail("Jurusan tidak ditemukan");
				}
			]
		 ]);
		
		if($validator->fails())
			return parent::res(false, null, null, $validator->errors());

		return parent::res(true, Prodi::getAllByJurusan($request, Jurusan::get($id_jurusan)));
	}
}
