<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;

/**
 * controller yang mengatur semua role / user
 * 
 */
class UserController extends Controller{

	function __construct(){
		/**
		 | menggunakan middleware standar auth
		 | 
		 */
		$this->middleware("auth");
	}

	/**
	 * menampilkan data user yang sedang login
	 * 
	 * @param Request $request
	 * 
	 * @return JSON
	 */
	public function self(Request $request){
		return parent::res(
			true,
			User::get($request)
		);
	}
}
