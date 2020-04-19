<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{
	User,
};
use App\{
	Repository\AdminRepository as Admin,
	Repository\RiwayatLoginRepository as Riwayat,
	Generator\JWT,
};
use \Validator;

class LoginController extends Controller
{
    function loginPost(Request $request)
    {

    	$validator = $this->dataValidate($request);

    	if($validator->fails())
    		return parent::res(!$validator->fails(), null, "", $validator->errors());

    	$input = collect($validator->getData());

    	$hasher = app()->make("hash");

    	$user = Admin::get()->byUsername($input->get("username"));

    	$collection = collect([]);


    	if( !$user ) {
    		//akun tidak ditemukan
    		Riwayat::insert( false, $request, $collection );
    		return parent::res(false, null, "Username dan password tidak cocok.");
    	}

    	if(!$hasher->check($input->get("password"), $user->password)){
    		//password salah

    		Riwayat::insert( false, $request, $collection, $user );
    		return parent::res(false, $user, "Username dan password tidak cocok.");
    	}

    	
    	$jwt = JWT::make( JWT::payload( User::ADMIN, "http://localhost:8000", $user->id, 0 ) );

    	$collection->put("token", $jwt->get("token"));


		Riwayat::insert( $jwt->get("status"), $request, $collection, $user );

		return parent::res(
			$jwt->get("status"),
			$jwt,
			$jwt->get("status") ? "✔" : "Token tidak dapat dibuat."
		);

    }

    public function dataValidate(Request $request)
    {
    	$validator = Validator::make(
    		$request->all(),
    		[
    			"username"	=> "required|min:4",
    			"password"	=> "required|min:4"
    		],
    		[
    			"required"	=> "Kolom :attribute tidak boleh kosong",
    		]
    	);
    	return $validator;
    }
}
