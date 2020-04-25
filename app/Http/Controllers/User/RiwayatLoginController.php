<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\RiwayatLoginRepository as Riwayat;
use Auth;
use App\User;

/**
 * riwayat login milik user
 * 
 */
class RiwayatLoginController extends Controller
{
	function __construct(){
		/**
		 * 
		 | middleware auth atau hanya user (siapa saja kecuali pengunjung)
		 | dan hanya user aktif saja
		 |
		 *
		 */
		$this->middleware('auth');
		$this->middleware('active');
	}


	/**
	 * melihat riwayat login user yang sedang login
	 * 
	 * @param Request $request
	 * 
	 * @return JSON
	 */
	function self(Request $request){
		return parent::res(
			true, 
			Riwayat::getByUser( 
				$request, 
				User::get($request)
			)
		);
	}

	/**
	 * melakukan logout masal pada perangkat lain
	 * menghapus semua data riwayat login kecuali token yang
	 * sedang aktif
	 * 
	 * @param Request $request
	 * 
	 * @return JSON
	 */
	function logoutAll(Request $request){
		$result = Riwayat::logoutAll(
			User::get($request), 
			User::getToken($request)
		);
		return parent::res(!!$result, User::get($request) );
	}


	/**
	 * melakukan logout pada sebuah perangkat spesifik
	 * 
	 * @param Request $request
	 * @param integer $id
	 * 
	 * @return JSON
	 */
	function logout(Request $request, $id){
		$result = Riwayat::logout($id, User::get($request));
		return parent::res(!!$result, User::get($request) );
	}
}
