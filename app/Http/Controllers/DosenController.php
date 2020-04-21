<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repository\{
	DosenRepository as Dosen,
};

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
}
