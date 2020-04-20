<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Repository\{
	JurusanRepository as Jurusan,
	InformasiJurusanRepository as InformasiJurusan,
};

class JurusanController extends Controller
{
	function __construct(){
		$this->middleware( 'auth' );
		$this->middleware( 'active' );
		$this->middleware( 'super' );
	}

    public function insert(Request $request){

    	$validator = self::insertValidate($request);

    	if($validator->fails())
    		return parent::res(!$validator->fails(), null, null, $validator->errors());

    	$data = self::getJurusanProps($validator);
    	$info = self::getInfoJurusanProps($validator);

    	$result = Jurusan::insert($data);
    	$jurusan = $result ? Jurusan::get($result) : null;

    	if($jurusan)
    		InformasiJurusan::insert($info, $jurusan);

    	return parent::res($result ? true : false, Jurusan::get($jurusan->id));
    }


    public function delete(Request $request, $id){
    	$result = Jurusan::delete($id);
    	return parent::res($result ? true : false, [ "undo" => false]);
    }

    public function update(Request $request, $id){

    	$validator = $this->updateValidate($request, $id);

    	if($validator->fails())
    		return parent::res( $validator->fails(), null, null, $validator->errors() );

    	$data = self::getJurusanProps($validator);
    	$info = self::getInfoJurusanProps($validator);

    	$result = Jurusan::update($data, $id);
    	$jurusan = Jurusan::get($id);

    	if($result)
    		InformasiJurusan::insert($info, $jurusan);

    	return parent::res( $result ? true : false, Jurusan::get($id) );
    }

    public static function updateValidate(Request $request, $id){

    	return Validator::make($request->all(), [
    		/**
    		 * for Jurusan
    		 * 
    		 * @param kd_jurusan
    		 * @param nama
    		 * @param keterangan
    		 * 
    		 */
    		"kd_jurusan" => [
    			"required",
    			"min:4",
    			function($attr, $val, $fail) use($id){
    				if(Jurusan::getByKdJurusanExcept($val, $id))
    					$fail("Kode jurusan sudah terdaftar.");
    			}
    		],
    		"nama"	=> "required|min:6",

    		/**
    		 * for Informasi Jurusan
    		 * 
    		 * @property email
    		 * @property website
    		 * @property keterangan
    		 * @property media_sosial
    		 * 
    		 * @todo media sosial JSON validation
    		 * 
    		 */

    		"email"	=> "email",
    	], [
    		'required' 	=> "Kolom :attribute tidak boleh kosong",
    		'min'		=> "Panjang :attribute setidaknya :min karakter",
    		'email'		=> "Kolom :attribute tidak valid "
    	]);
    }


    public static function insertValidate(Request $request){
    	return Validator::make($request->all(), [

    		/**
    		 * for Jurusan
    		 * 
    		 * @property kd_jurusan
    		 * @property nama
    		 * @property keterangan
    		 * 
    		 */
    		'kd_jurusan' => [
    			"required",
    			"min:4",
    			function($attr, $val, $fail){
    				if(preg_match("/[^a-zA-Z0-9\-\_]/im", $val))
    					$fail("Pastikan kode jurusan tidak mengandung spasi atau karakter spesial");
    			},
    			function($attr, $val, $fail){
    				if(Jurusan::getByKdJurusan(trim($val)))
    					return $fail("Kode Jurusan sudah terdaftar.");
    			},
    		],
    		"nama"	=> "required|min:6",


    		/**
    		 * for Informasi Jurusan
    		 * 
    		 * @property email
    		 * @property website
    		 * @property keterangan
    		 * @property media_sosial
    		 * 
    		 */
    		"email"	=> "email",
    	], [
    		'required' 	=> "Kolom :attribute tidak boleh kosong",
    		'min'		=> "Panjang :attribute setidaknya :min karakter",
    		'email'		=> "Kolom :attribute tidak valid "
    	]);
    }
    

    public static function getJurusanProps($validator){
    	return collect( $validator->getData() )->only([ "kd_jurusan", "nama" ]);
    }
    public static function getInfoJurusanProps($validator){
    	return collect( $validator->getData() )->only([ "email", "website", "keterangan", "media_sosial" ]);
    }
}
