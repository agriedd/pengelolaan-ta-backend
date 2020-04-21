<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Auth;
use App\Http\Controllers\{
	Controller,
	SuperAdmin\ProdiController as SuperAdminProdiController,
};
use App\Repository\{
	ProdiRepository as Prodi,
	InformasiProdiRepository as InformasiProdi,
};
use Validator;

class ProdiController extends Controller
{
	function __construct()
	{
		$this->middleware("auth");
		$this->middleware("active");
		// $this->middleware("admin");
	}
    public function update(Request $request, $id){
    	if(Auth::user()->level)
    		return self::updateSuperAdmin($request, $id);

    	$request->request->add([ "id" => $id ]);

    	$validator = self::updateValidate($request);

    	if($validator->fails())
    		return parent::res(false, null, null, $validator->errors());

    	$data = self::getProdiProps($validator);
    	$info = self::getInfoProdiProps($validator);

    	$prodi = Prodi::get($id);
    	$result = InformasiProdi::insert($info, $prodi);

    	return parent::res($result ? true : false, Prodi::get($id));
    }
    static function updateSuperAdmin(Request $request, $id)
    {
    	$updateController = new SuperAdminProdiController();
    	return $updateController->update($request, $id);
    }
    static function updateValidate(Request $request){
    	return Validator::make( $request->all(), [
    		/**
    		 * admin jurusan hanya boleh mengubah informasi jurusannya sendiri
    		 * 
    		 * @property email
    		 * @property telepon
    		 * @property media_sosial
    		 * @property keterangan
    		 * 
    		 */
    		"email"		=> 'email',
    		"media_sosial"	=> "json",
    		"id"		=> [
    			"required",
    			function($attribute, $value, $fail){
    				if(!Prodi::get($value))
    					return $fail("🔎 Prodi tidak ditemukan");
    			},
    			function($attribute, $value, $fail){
    				$prodi = Prodi::get($value);
    				if($prodi && ( $prodi->jurusan->id !== Auth::user()->jurusan_id && !Auth::user()->level))
    					return $fail("😑 Anda tidak dapat mengubah data prodi ini.");
    			}
    		],
    	], [
    		"required" =>	"Kolom :attribute tidak boleh kosong",
    	]);
    }
    static function getProdiProps($validator)
    {
    	return collect($validator->getData())->only([]);
    }
    static function getInfoProdiProps($validator)
    {
    	return collect($validator->getData())->only([ "email", "media_sosial", "telepon", "keterangan" ]);
    }
}
