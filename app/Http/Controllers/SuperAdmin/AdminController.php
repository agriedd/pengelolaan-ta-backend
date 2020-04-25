<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\{
	AdminRepository as Admin,
    JurusanRepository as Jurusan,
	InformasiAdminRepository as InformasiAdmin,
};
use Validator;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;


/**
 * 🔥 class admin kontroller khusus super admin 🔥
 * 
 * @todo menambah softDeletes pada model admin
 * @todo menambah method delete admin
 * 
 */
class AdminController extends Controller
{

	function __construct(){
		$this->middleware("auth");
		$this->middleware("active");
		$this->middleware("super");
	}

    /**
     * controller untuk menambah sebuah data admin baru
     * 
     * @param $request
     * 		@property username 		string, minimal 6 karakter dan maksimal 20 karakter
     * 		@property password 		string, minimal 6 karakter
     * 
     * @return json { status: boolean, error: bollean, data: Modal\Admin } 
     * 
     */
    function insert(Request $request){

    	$validator = self::insertValidate($request);


    	//jika validasi gagal
    	if($validator->fails())
    		return $this->res(false, null, "", $validator->errors());

        $data = self::getAdminProps($validator);
        $info = self::getInfoAdminProps($validator);

        /**
         * @var result
         * 
         * @todo bersihkan cache admin get ketika informasi admin update
         * 
         */
    	$result = Admin::insert( $data );
    	$admin = $result ? Admin::get($result) : null;

        if($admin)
            InformasiAdmin::insert($info, $admin);

    	return parent::res(!!$admin, Admin::get($admin->id));
    }


    /**
     * menambah data admin dengan menggunakan informasi dosen
     * username dan password mungkin akan sama dengan data dosen
     * 
     * @todo buat model Dosen
     * @todo tambah relasi model informasi admin dengan dosen morph 1-1
     * @todo tambah relasi model admin dengan dosen morph 1-1
     * 
     * @todo tambah tabel pegawai
     * @todo tambah method insertByPegawai
     * 
     */
    function insertByDosen(Request $request, $id){

        $request->request->add(["id" => null]);

        $validator = self::insertValidate($request);

        if($validator->fails())
            return parent::res(!$validator->fails(), null, null, $validator->errors());

        $data = self::getAdminProps($validator);
        $info = self::getInfoAdminProps($validator);

        $result = Admin::insert( $data );

        $admin = $result ? Admin::get($result) : null;

        if($admin)
            Dosen::get($id)->admin()->create( $info );

        return parent::res(!!$admin, $admin);
    }

    /**
     * validasi request yang diterima sebelum data ditambahkan
     * 
     * @param request
     * 
     * @return Validator
     * 
     */
    static function insertValidate(Request $request){
        return Validator::make(
            $request->all(),
            [

                /**
                 * property yang diperbolehkan untuk menambah data admin
                 * sebagai super admin
                 * 
                 * @property username
                 * @property password
                 * @property id_jurusan
                 * 
                 */
                "username" => 
                    [
                        "required", "min:6",
                        function($attr, $val, $fail){
                            if(Admin::get()->byUsername($val))
                                $fail("{$attr} `{$val}` sudah digunakan.");
                        }
                    ],
                "password" => "required|min:6",
                "id_jurusan"    =>
                    [
                        "nullable",
                        function($attr, $val, $fail){
                            if(!Jurusan::get($val))
                                $fail("Jurusan tidak ditemukan");
                        }
                    ],

                /**
                 * property yang diperbolehkan untuk menambah informasi admin
                 * sebagai super admin
                 * 
                 * @property user_id
                 * @property user_type
                 * 
                 * atau
                 * 
                 * @property nama
                 * @property nip
                 * 
                 * dengan
                 * 
                 * @property status
                 * @property level
                 * 
                 * @todo validasi nip jika ada
                 * @todo add model dosen
                 * @todo check dosen by id
                 * 
                 */
                "nama"      => "min:4",
                "nip"       => "min:18|max:18",
                "id"        => function($attr, $val, $fail){
                    if(!strlen($val))
                        $fail("the id is empty");
                }
            ],
            [
                "required"  => "Membutuhkan :attribute",
                "min"       => "Panjang :attribute setidaknya :min karakter",
                "max"       => "Panjang :attribute tidak boleh melebihi :max karakter",
            ]
        );
    }

    /**
     * mengambil data hasil validasi untuk model Admin
     * 
     * @param Validator validator
     * 
     * @return Collection
     * 
     */
    static function getAdminProps($validator){
        return collect($validator->getData())->only([ "username", "password", "id_jurusan" ]);
    }

    /**
     * mengambil data hasil validasi untuk informasi Admin
     * 
     * @param Validator validator
     * 
     * @return Collection
     * 
     */
    static function getInfoAdminProps($validator){
        return collect($validator->getData())->only([ "nama", "nip", "status", "level", "user_id", "user_type" ]);
    }

    /**
     * method untuk mengupdate data admin oleh super admin
     * 
     * @param $request
     * @param $id
     * 
     * @return json {status: boolean, data: Model\Admin}
     * 
     * 
     * ⚠
     * 
     * peng-update-an informasi admin tidak benar
     * data baru yang ditambahkan tidak mengikuti data
     * informasi lama sehingga ketika mengupdate informasi
     * admin salah satu kolom saja, kolom lainnya akan menjadi
     * null, walaupun pada informasi sebelumnya informasi sudah
     * diset.
     * 
     * ✔
     * 
     * sementara dapat diakali dengan mengisi form update dengan
     * data lama.
     * 
     * @todo yakin masalah di atas dapat berjalan
     *
     */
    function update(Request $request, $id){

    	$request->request->add([ "id" => $id ]);

    	$validator = self::updateValidate($request);

    	if($validator->fails())
    		return $this->res(false, null, null, $validator->errors());

    	$data = self::getAdminProps($validator);
        $info = self::getInfoAdminProps($validator);

    	/**
    	 * @var result_admin 				mengupdate data admin($id)
    	 * @var result_informasi_admin		menambahkan data informasi_admin($id) 
    	 * 
    	 */
    	$result_admin 			= Admin::update($data, $id);
    	$result_informasi_admin = InformasiAdmin::insert($info, Admin::get($id));

    	$result = $result_admin && $result_informasi_admin;

		return parent::res($result, Admin::get($id));
    }

    function updateValidate(Request $request){
    	return Validator::make(
    		$request->all(),
    		[

    			/**
    			 * admin \App\Model\Admin
    			 * 
                 * 
    			 */
    			"username" 	=> [
    				"min:6",
    				function($attr, $val, $fail) use($request){

    					if(Admin::getByUsernameExcept($val, $request->id))
    						$fail("{$attr} `{$val}` sudah digunakan.");
    					
    				}
    			],
    			"password" 	=> "min:6",
                
                "id_jurusan" => [
                    "nullable",
                    function($attr, $val, $fail){
                        if(!Jurusan::get($val))
                            $fail("Jurusan tidak ditemukan");
                    }
                ],

    			"id"		=> [
    				"required",
    				function($attr, $val, $fail){
    					$admin = Admin::get($val);
    					if(!$admin)
    						return $fail("Admin dengan id {$val} tidak ditemukan");
                        if($admin->id != User::get()->id && $admin->level)
                            return $fail("Terjadi sebuah kesalahan");
    				}
    			],


    			/**
    			 * @property informasi admin \App\Model\InformasiAdmin
    			 * 
    			 */
    			"nama" 		=> "min:4",
    			"nip" 		=> "min:18|max:18"
    		],
    		[
    			"min" 		=> "Panjang :attribute setidaknya :min karakter",
    			"max" 		=> "Panjang :attribute tidak boleh lebih dari :max karakter",
    		]
    	);
    }

    function delete(Request $request, $id){
    	$validator = self::deleteValidate($request);

    	if($validator->fails())
    		return parent::res(false, null, null, $validator->errors());

    	$result = Admin::delete($id);

    	return parent::res(!!$result, [ "undo" => false ]);
    }

    function resetPassword(Request $request, $id){

        $request->request->add(["id" => $id]);

        $validator = self::updateValidate( $request );

        if($validator->fails())
            return parent::res(false, null, null, $validator->errors());

        $result = Admin::resetPassword($id);

        return parent::res(!!$result, Admin::get($id));
    }
}
