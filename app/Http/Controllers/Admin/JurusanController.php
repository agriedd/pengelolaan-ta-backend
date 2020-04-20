<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\{
	JurusanRepository as Jurusan,
	InformasiJurusanRepository as InfoJurusan,
};
use App\Http\Controllers\{
	SuperAdmin\JurusanController as SuperAdmin,
};
use Auth;

class JurusanController extends Controller
{

	public function __construct(){
		$this->middleware("auth");
		$this->middleware("active");
		$this->middleware("admin");
		$this->middleware("jurusanorsuper");
	}

	public function update(Request $request, $id){
		if(Auth::user()->level)
			return $this->updateAsSuperAdmin($request, $id);

		$validator = self::updateValidate($request, $id);

		if($validator->fails())
			parent::res($validator->fails(), null, null, $validator->errors());

		$result = InfoJurusan::insert(self::getInfoJurusanProps($validator), Jurusan::get($id));

		return parent::res($result ? true : false, Jurusan::get($id));

	}

	public static function updateValidate(Request $request, $id){
		return Validator::make( $request->all(), [
				/**
				 * only For InformasiAdmin
				 * 
				 * @param email
				 * @param keterangan
				 * @param website
				 * @param media_sosial
				 * 
				 * @todo media_sosial json
				 * @todo website url
				 * 
				 */

				'email' => "email",
			], [
				"emial" => "Format email tidak valid"
			]
		);
	}

    public function updateByKdJurusan(Request $request, $kd_jurusan){
    	return $this->update( $request, Jurusan::getByKdJurusan($kd_jurusan)->id );
    }

    public static function getInfoJurusanProps($validator){
    	return collect($validator->getData())->only([ "email", "keterangan", "website", "media_sosial" ]);
    }

	public function updateAsSuperAdmin(Request $request, $id){
		$jurusanController = new SuperAdmin();
		return $jurusanController->update($request, $id);
	}
}
