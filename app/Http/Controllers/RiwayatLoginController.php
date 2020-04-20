<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\RiwayatLoginRepository as Riwayat;

class RiwayatLoginController extends Controller
{

	function __construct(){
		$this->middleware('auth');
		$this->middleware('active');
		$this->middleware('super');
	}

	/**
	 * mengambil semua data riwayat login
	 * 
	 * @param Request request
	 * 
	 * @return json
	 */
    function getAll(Request $request){
    	$data = Riwayat::getAll($request);
    	return parent::res( true, $data );
    }
}
