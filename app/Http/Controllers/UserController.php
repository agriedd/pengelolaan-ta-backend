<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class UserController extends Controller{

	function __construct()
	{
		$this->middleware("auth:admin");
	}

	public function self(Request $request){
		return parent::res(true, Auth::user());
	}
}
