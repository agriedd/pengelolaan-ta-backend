<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\{
	JurusanRepository as Jurusan,
};

class JurusanController extends Controller
{

	public function get(Request $request, $id){
		return parent::res( true,  Jurusan::get($id) );
	}
	public function getByKdJurusan(Request $request, $kd_jurusan){
		return parent::res( true,  Jurusan::getByKdJurusan($kd_jurusan) );
	}
	
	public function getAll(Request $request){
		$data = Jurusan::getAll($request);
		return parent::res( true, $data );
	}
}
