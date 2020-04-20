<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\{
	AdminRepository as Admin,
	InformasiAdminRepository as InformasiAdmin,
};
use Validator;
use Auth;
use Illuminate\Support\Collection;


/**
 * 🔥 kelas admin kontroller khusus super admin 🔥
 * 
 * @todo menambah softDeletes pada model admin
 * @todo menambah method delete admin
 * 
 */
class AdminController extends Controller
{
    private $update_rule;


	/**
	 * mengambil data admin dengan spesifik id
     * 
	 * @param Request request
	 * @param integer id
	 * 
	 * @return json { status: boolean, data: Model\Admin }
	 * 
	 */
    function get(Request $request, $id)
    {
    	$data 	= Admin::get($id);
    	$status = $data ? true : false;
    	return parent::res($status, $data);
    }


    /**
     * mengambil beberapa data admin
     * 
     * @param $request
     * 
     * @return json { status: boolean, data: Array }
     * 
     * @todo add filter
     * 
     */
    function getAll(Request $request){
    	$data 	= Admin::getAll();
    	$status = $data ? true : false;
    	return parent::res($status, $data);
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
    		return $this->res($validator->fails(), null, "", $validator->errors());

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

    	return parent::res($admin ? true : false, Admin::get($id));
    }


    /**
     * menambah data admin dengan menggunakan informasi dosen
     * 
     * 
     */
    function insertByDosen(Request $request, $id){

        $request->request->add(["id" => null]);

        $validator = self::insertValidate($request);

        if($validator->fails())
            return parent::res(!$validator->fails(), null, null, $validator->errors());

        die();

        $data = self::getAdminProps($validator);
        $info = self::getInfoAdminProps($validator);

        $result = Admin::insert( $data );

        $admin = $result ? Admin::get($result) : null;

        if($admin)
            Dosen::get($id)->admin()->create( $info );

        return parent::res($admin ? true : false, $admin);
    }

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

    static function getAdminProps($validator){
        return collect($validator->getData())->only([ "username", "password" ]);
    }
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

    	$validator = Validator::make(
    		$request->all(),
    		[

    			/**
    			 * @property admin \App\Model\Admin
    			 * 
    			 */
    			"username" 	=> [
    				"min:6",
    				function($attr, $val, $fail) use($id){

    					if(Admin::getByUsernameExcept($val, $id))
    						$fail("{$attr} `{$val}` sudah digunakan.");
    					
    				}
    			],
    			"password" 	=> "min:6",


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

    	if($validator->fails())
    		return $this->res($validator->fails(), null, null, $validator->errors());

    	$data = Collection::make($validator->getData());

    	$data_admin 			= Collection::make($data->only(["username", "password"]));
    	$data_informasi_admin 	= Collection::make($data->only([ "nama", "nip", "username", "level", "status" ]));

    	/**
    	 * @var result_admin 				mengupdate data admin($id)
    	 * @var result_informasi_admin		menambahkan data informasi_admin($id) 
    	 * 
    	 */
    	$result_admin 			= Admin::update($data_admin, $id);
    	$result_informasi_admin = InformasiAdmin::insert($data_informasi_admin, Admin::get($id));

    	$result = $result_admin && $result_informasi_admin;

		return parent::res($result, Admin::get($id));
    }
}
