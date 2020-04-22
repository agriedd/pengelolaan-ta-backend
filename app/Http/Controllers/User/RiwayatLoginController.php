<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\RiwayatLoginRepository as Riwayat;
use Auth;

class RiwayatLoginController extends Controller
{
	function __construct(){
		$this->middleware('auth');
		$this->middleware('active');
	}

	function self(Request $request){
		return parent::res(true, Riwayat::getByUser( $request, Auth::user() ));
	}
}
