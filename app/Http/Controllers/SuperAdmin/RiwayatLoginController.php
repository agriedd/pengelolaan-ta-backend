<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\{
	RiwayatLoginRepository as Riwayat,
	AdminRepository as Admin,
};

class RiwayatLoginController extends Controller
{
	
	function __construct(){
		$this->middleware('auth:admin');
		// $this->middleware('active');
		// $this->middleware('super');
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

    function clearAdmin($id){
    	$data = Riwayat::logoutAll( Admin::get($id) );
    	return parent::res( !!$data, Admin::get($id) );
    }
}
