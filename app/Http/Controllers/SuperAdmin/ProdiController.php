<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\{
	ProdiRepository as Prodi,
	JurusanRepository as Jurusan,
	InformasiProdiRepository as InformasiProdi,
};

use Validator;

class ProdiController extends Controller
{
	function __construct(){
		$this->middleware( 'auth' );
		$this->middleware( 'active' );
		$this->middleware( 'super' );
	}

	public function insert(Request $request)
	{
		$validator = self::insertValidate($request);

		if($validator->fails())
			return parent::res(false, null, null, $validator->errors());

		$data = self::getProdiProps($validator);
		$info = self::getInfoProdiProps($validator);

		$prodi = Prodi::insert($data, Jurusan::get($request->id_jurusan));

		if($prodi)
			InformasiProdi::insert($info, $prodi);

		return parent::res($prodi ? true : false, Prodi::get($prodi->id));
	}

	static function insertValidate(Request $request){
		return Validator::make($request->all(), [
			/**
			 * for model Prodi
			 * 
			 * @property kd_prodi
			 * @property nama
			 * @property id_jurusan
			 * 
			 * @todo tambah kd_prodi
			 * 
			 */
			// "kd_prodi"	=> [
			// 	"required",
			// 	function($attribute, $value, $fail){
			// 		if(preg_match("/[^a-zA-Z0-9\-\_]/im", $value))
			// 			$fail("🤔 Kode Prodi tidak boleh mengandung spasi atau karakter khusus");
			// 	},
			// 	function($attribute, $value, $fail){
			// 		if(Prodi::getByKdProdi($value))
			// 			$fail("⚠ Kode Prodi sudah terdaftar");
			// 	}
			// ],
			"nama"		=> "required|min:3",
			"id_jurusan"=> [
				"required",
				function($attribute, $value, $fail){
					if(!Jurusan::get($value))
						$fail("🔎 Jurusan tidak ditemukan.");
				}
			],

			/**
			 * for model Informasi Prodi
			 * 
			 * @property email
			 * @property telepon
			 * @property media_sosial
			 * @property keterangan
			 * 
			 */
			"email"			=> "email",
			"media_sosial"	=> "json"
		], [
			"required"		=> "Kolom :attribute tidak boleh kosong",
			"min"			=> "Panjang karakter kolom :attribute tidak boleh kurang dari :min karakter",
			"email"			=> "Email tidak valid",
		]);
	}

	public function update(Request $request, $id){
		$request->request->add([ "id" => $id ]);

		$validator = self::updateValidate($request);

		if($validator->fails())
			return parent::res(false, null, null, $validator->errors());

		$data = self::getProdiProps($validator);
		$info = self::getInfoProdiProps($validator);

		$result = Prodi::update($data, $id);
		if($result)
			InformasiProdi::insert($info, Prodi::get($id));

		return parent::res($result ? true : false, Prodi::get($id));

	}

	static function updateValidate(Request $request){
		return Validator::make( $request->all(), [
			/**
			 * for Prodi
			 * 
			 * @property nama
			 * @property id_jurusan
			 *
			 * @todo cek kesamaan nama prodi
			 * 
			 */
			"id"	=> [
				function($attribute, $value, $fails){
					if(!Prodi::get($value))
						return $fails("Prodi tidak ditemukan");
				}
			],
			"nama"	=> "min:3",
			"id_jurusan"	=> [
				function($attribute, $value, $fails){
					if(!Jurusan::get($value))
						return $fails("Jurusan tidak ditemukan");
				}
			],

			/**
			 * for Informasi Prodi
			 * 
			 * @property email
			 * @property telepon
			 * @property media_sosial
			 * @property keterangan
			 * 
			 */
			"email"			=> "email",
			"media_sosial"	=> "json",
		], [
			"min"	=> "Kolom :attribute tidak boleh kurang dari :min karakter",
			"email"	=> "Email tidak valid",
		]);
	}

	static function getProdiProps($validator)
	{
		return collect($validator->getData())->only(["nama", "id_jurusan"]);
	}
	static function getInfoProdiProps($validator)
	{
		return collect($validator->getData())->only(["email", "telepon", "media_sosial", "keterangan"]);
	}
}
