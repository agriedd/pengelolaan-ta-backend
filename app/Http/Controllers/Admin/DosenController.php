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

use App\User;

class DosenController extends Controller
{

    function __construct(){
        /*
         | kontroller hanya dapat diakses admin jurusan saja
         |
         */
        $this->middleware("auth");
        $this->middleware("active");
        $this->middleware("adminjurusan");
    }

    /**
     * menambah data dosen oleh admin jurusan
     * 
     * @param Request $request
     * 
     * @return JSON response
     */
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

    	return parent::res(!!$result, Dosen::get($dosen->id));
    }


    /**
     * validasi untuk insert method
     * 
     * @param Request $request
     * 
     * @return Validator
     */
    static function insertValidate(Request $request){
    	$rules = [
    		/**
    		 * untuk model Dosen
    		 * 
    		 * @property usename
    		 * @property password
    		 * 
    		 * @todo pengecekan format password
    		 */
    		"username"	=> [
    			"required",
    			"min:6",
    			function($attr, $val, $fail){
    				if(Dosen::getByUsername($val))
                        /*
                         | jika username sudah terdaftar
                         |
                         */
    					$fail("Username sudah terdaftar");
    			}
    		],
    		"password"	=> "required|min:6",
            "id_prodi"  => [
                "required",
                function($attr, $val, $fails){
                    $prodi = Prodi::get($val);
                    if(!$prodi)
                        /*
                         | jika prodi tidak ditemukan
                         |
                         */
                        return $fails("Prodi tidak ditemukan");
                    if($prodi->jurusan->id !== User::get()->id_jurusan)
                        /*
                         | jika prodi yang dipilih tidak sama dengan
                         | jurusan admin jurusan
                         |
                         */
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
    		"nip"			=> "nullable|min:18|max:18",
    		"jenis_kelamin"	=> [
    			Rule::in([ "L", "P" ])
    		],
    		"tanggal_lahir" => "date",
    		"email"			=> "email",
    		"media_sosial"	=> "json",
    	];
        $messages = [
    		"required" 	=> "Kolom :attribute tidak boleh kosong",
    		"min" 		=> "Kolom :attribute tidak boleh kurang dari :min karakter",
    		"max" 		=> "Kolom :attribute tidak boleh melebihi :max karakter",
    		"email" 	=> "Kolom email tidak valid",
    		"date"		=> "Kolom :attribute harus memiliki format tanggal yang valid"
    	];
        return Validator::make(
            $request->all(),
            $rules,
            $messages
        );
    }


    /**
     * method update dosen
     * 
     * @param Request $request
     * @param integer $id
     * 
     * @return Json response
     */
    function update(Request $request, $id){
        
        $request->request->add(["id" => $id]);

        $validator = self::updateValidate($request);

        if($validator->fails())
            return parent::res(false, null, null, $validator->errors());

        $data = self::getDosenProps($validator);
        $info = self::getInfoDosenProps($validator);

        $result = Dosen::update($data, $id);
        $dosen  = $result ? Dosen::get($id) : null;

        if($dosen)
            InformasiDosen::insert($info, $dosen);

        return parent::res(!!$result, Dosen::get($dosen->id));
    }


    /**
     * validasi untuk update method
     * 
     * @param Request $request
     * @return Validator
     */
    static function updateValidate(Request $request){
        return Validator::make($request->all(), [
            /**
             * untuk model Dosen
             * 
             * @property usename
             * @property password
             * @property id_prodi
             * @property id
             * 
             * @todo pengecekan format password
             * 
             */
            "username"  => [
                "min:6",
                "nullable",
                function($attr, $val, $fail){
                    if(Dosen::getByUsername($val))
                        $fail("Username sudah terdaftar");
                }
            ],
            "password"  => "nullable|min:6",
            "id"        => [
                "required",
                function($attr, $val, $fail){
                    $dosen = Dosen::get($val);
                    if(!$dosen)
                        return $fail("Tidak menemukan dosen dengan id {$val}");
                    if($dosen->prodi->jurusan->id !== Auth::user()->id_jurusan)
                        /*
                         | jika jurusan - prodi dosen yang diupdate tidak sama dengan
                         | jurusan admin jurusan
                         |
                         */
                        return $fail("Tidak menemukan dosen dengan id {$val}");
                },
            ],
            "id_prodi"  => [
                function($attr, $val, $fails){
                    $prodi = Prodi::get($val);

                    if(!$prodi)
                        return $fails("Prodi tidak ditemukan");

                    if($prodi->jurusan->id !== User::get()->id_jurusan)
                        /*
                         | jika prodi tidak tidak sama dengan dengan jurusan
                         | admin jurusan
                         |
                         */
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
            "nama"          => "nullable",
            "nip"           => "nullable|min:18|max:18",
            "jenis_kelamin" => [
                Rule::in([ "L", "P" ])
            ],
            "tanggal_lahir" => "date",
            "email"         => "email",
            "media_sosial"  => "json",
        ],[
            "required"  => "Kolom :attribute tidak boleh kosong",
            "min"       => "Kolom :attribute tidak boleh kurang dari :min karakter",
            "max"       => "Kolom :attribute tidak boleh melebihi :max karakter",
            "email"     => "Kolom email tidak valid",
            "date"      => "Kolom :attribute harus memiliki format tanggal yang valid"
        ]);
    }

    /**
     * mengambil data hasil validasi untuk di store
     * ke model Dosen
     * 
     * @param Validator $validator
     * 
     * @return Collection
     */
    static function getDosenProps($validator){
    	return collect($validator->getData())
            ->only([ "username", "password", "id_prodi" ]);
    }

    /**
     * mengambil data hasil validasi untuk di store
     * ke model InformasiDosen
     * 
     * @param Validator $validator
     * 
     * @return Collection
     */
    static function getInfoDosenProps($validator){
    	return collect($validator->getData())
            ->only([
                "nama", 
                "prefix", 
                "sufiks", 
                "nip", 
                "jenis_kelamin", 
                "tempat_lahir", 
                "tanggal_lahir", 
                "alamat", 
                "foto", 
                "email", 
                "telepon", 
                "media_sosial", 
                "biodata"
            ]);
    }

    /**
     * untuk menghapus dosen jurusan tertentu
     * 
     * @param Request $request
     * 
     * @return JSON response
     */
    public function delete(Request $request, $id){
        $request->request->add(["id" => $id]);

        $validator = Validator::make($request->all(), [
            "id" => [
                "required",
                function($attribute, $value, $fail){
                    $dosen = Dosen::get($value);
                    if(!$dosen)
                        return $fail("Dosen tidak ditemukan");
                    if($dosen->prodi->jurusan->id !== User::get()->id_jurusan)
                        return $fail("Anda tidak dapat menghapus data ini");
                }
            ]
        ]);
        if($validator->fail())
            return parent::res(false, null, null, $validator->errors());

        $result = Dosen::delete($id);
        return parent::res(!!$result, [ "undo" => false ]);
    }
}
