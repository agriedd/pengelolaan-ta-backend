<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{
	User,
};
use App\{
	Repository\AdminRepository as Admin,
    Repository\DosenRepository as Dosen,
	Repository\RiwayatLoginRepository as Riwayat,
	Generator\JWT,
};
use Illuminate\Validation\Rule;
use Validator;
use Auth;


class LoginController extends Controller
{
    /**
     * proses login terjadi disini
     * 
     * @param request Request
     * 
     * @todo filter login untuk mahasiswa dan dosen
     */
    function loginPost(Request $request){
        /**
         | melakukan validasi request
         | dan jika gagal respon dengan pesan error
         | 
         */
    	$validator = self::dataValidate($request);
    	if($validator->fails())
    		return parent::res(!$validator->fails(), null, "❌", $validator->errors());

    	$input         = collect($validator->getData());
    	$hasher        = app()->make("hash");
    	$user          = self::getUser($request);

    	if(!$user) {
            /*
             | jika user dengan username request tidak ditemukan
             | 
             */
    		Riwayat::insert( false, $request );
    		return parent::res(false, null, "Username dan password tidak cocok.");
    	}

    	if(!$hasher->check($input->get("password"), $user->password)){
    		/*
             | jika password tidak cocok
             |
             */ 
    		Riwayat::insert( false, $request, $user );
    		return parent::res(false, $user, "Username dan password tidak cocok.");
    	}


        /**
         * membuat token dengan menyimpan beberapa parameter
         * 
         * @param type
         * @param referer
         * @param user
         */
    	$jwt = JWT::make(
            JWT::payload(
                $request->type,
                $request->server("HTTP_REFERER"),
                $user
            )
        );

    	$request->request->add(["token" => $jwt->get("token")]);
		Riwayat::insert( $jwt->get("status"), $request, $user );

		return parent::res(
			$jwt->get("status"),
			$jwt,
			$jwt->get("status") ? "✔" : "Token tidak dapat dibuat."
		);

    }


    /**
     * validasi data login
     * 
     * @param request
     * 
     * @return \Validator
     * 
     */
    public static function dataValidate(Request $request){
        $rule = [
            "username"  => "required|min:4",
            "password"  => "required|min:4",
            "type"      => [
                "nullable",
                Rule::in([User::ADMIN, User::DOSEN, User::MAHASISWA]),
            ],
        ];

        $message = [
            "required"  => "Kolom :attribute tidak boleh kosong",
            "in"        => "Pastikan kolom tipe sesuai dengan format",
        ];

    	return Validator::make($request->all(), $rule, $message);
    }

    /**
     * mengambil type user request
     * 
     * @param Request $request
     * 
     * @return fixed
     * 
     * @todo tambah jenis mahasiswa
     */
    public function getUser(Request $request){
        if($request->type === User::ADMIN){
            /**
             | jika request type == "admin"
             | 
             */
            return Admin::get()->byUsername($request->username);
        } elseif($request->type === User::DOSEN){
            /**
             | jika request type == "dosen"
             | 
             */
            return Dosen::getByUsername($request->username);
        } elseif($request->type === User::MAHASISWA || !$request->has("type")){
            /**
             | jika request type == "mahasiswa"
             | 
             */
            return null;
        }
    }
}
