<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;

use App\Repository\{
	DosenRepository as Dosen,
    ProdiRepository as Prodi,
	InformasiDosenRepository as InformasiDosen,
};

use Auth;

class DosenController extends Controller
{

    function __construct(){
        $this->middleware("auth");
        $this->middleware("active");
        $this->middleware("adminjurusan");
    }

    function insert(Request $request){
    	
    	$validator = self::insertValidate($request);

    	if($validator->fails())
    		return parent::res(false, null, null, $validator->errors());

    	$data = self::getDosenProps($validator);
    	$info = self::getInfoDosenProps($validator);

    	$result = Dosen::insert($data);
    	$dosen 	= $result ? Dosen::get($result) : null;

    	if($dosen)
    		InformasiDosen::insert($info, $dosen);

    	return parent::res($result ? true : false, Dosen::get($dosen->id));
    }

    static function insertValidate(Request $request){
    	return Validator::make($request->all(), [
    		/**
    		 * untuk model Dosen
    		 * 
    		 * @property usename
    		 * @property password
    		 * 
    		 * @todo pengecekan format password
    		 * 
    		 */
    		"username"	=> [
    			"required",
    			"min:6",
    			function($attr, $val, $fail){
    				if(Dosen::getByUsername($val))
    					$fail("Username sudah terdaftar");
    			}
    		],
    		"password"	=> "required|min:6",
            "id_prodi"  => [
                "required",
                function($attr, $val, $fails){
                    $prodi = Prodi::get($val);

                    if(!$prodi)
                        return $fails("Prodi tidak ditemukan");

                    if($prodi->jurusan->id !== Auth::user()->id_jurusan)
                        return $fails("Anda tidak memiliki akses untuk prodi ini");
                },
            ],
    		/**
    		 * untuk model Informasi Dosen
    		 * 
    		 * @property nama
    		 * @property prefix
    		 * @property sufiks
    		 * @property nip
    		 * @property jenis_kelamin
    		 * @property tempat_lahir
    		 * @property tanggal_lahir
    		 * @property alamat
    		 * @property foto
    		 * @property email
    		 * @property telepon
    		 * @property media_sosial
    		 * @property biodata
    		 * 
    		 */
    		"nama"			=> "required",
    		"nip"			=> "min:18|max:18",
    		"jenis_kelamin"	=> [
    			Rule::in([ "L", "P" ])
    		],
    		"tanggal_lahir" => "date",
    		"email"			=> "email",
    		"media_sosial"	=> "json",
    	],[
    		"required" 	=> "Kolom :attribute tidak boleh kosong",
    		"min" 		=> "Kolom :attribute tidak boleh kurang dari :min karakter",
    		"max" 		=> "Kolom :attribute tidak boleh melebihi :max karakter",
    		"email" 	=> "Kolom email tidak valid",
    		"date"		=> "Kolom :attribute harus memiliki format tanggal yang valid"
    	]);
    }

    static function getDosenProps($validator){
    	return collect($validator->getData())->only([ "username", "password", "id_prodi" ]);
    }
    static function getInfoDosenProps($validator){
    	return collect($validator->getData())->only([ "nama", "prefix", "sufiks", "nip", "jenis_kelamin", "tempat_lahir", "tanggal_lahir", "alamat", "foto", "email", "telepon", "media_sosial", "biodata" ]);
    }
}
