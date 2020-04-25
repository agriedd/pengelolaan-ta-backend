<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\{
    Controller,
    SuperAdmin\AdminController as SuperAdminController,
};
use Illuminate\Http\Request;
use App\Repository\{
	AdminRepository as Admin,
	InformasiAdminRepository as InformasiAdmin,
};
use App\User;
use Validator;
use Auth;


class AdminController extends Controller
{
    function __construct(){
        /*
         | hanya dapat diakes oleh admin yang aktif saja
         | 
         */
        $this->middleware("auth:admin");
        $this->middleware("active");
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
        return collect($validator->getData())
            ->only([ "username", "password" ]);
    }


    /**
     * mengambil data hasil validasi untuk informasi Admin
     * 
     * @param Validator validator
     * 
     * @return Collection
     */
    static function getInfoAdminProps($validator){
        return collect($validator->getData())
            ->only([ "nama", "nip" ]);
    }


    /**
     * validasi untuk method update
     * 
     * @param Request $request
     * 
     * @return Validator
     */
    static function updateValidate(Request $request){

        $rule = [
            /**
             * admin \App\Model\Admin
             * 
             * @property username
             * @property password
             * @property id
             * 
             */
            "username"  => [
                "min:6",
                function($attr, $val, $fail) use($request){
                    /*
                     | mengecek apakah username sudah digunakan admin lain,
                     | selain admin ini.
                     |
                     */
                    if(Admin::getByUsernameExcept($val, $request->id))
                        $fail("{$attr} `{$val}` sudah digunakan.");
                    
                }
            ],
            "password"  => "min:6",
            "id"        => [
                function($attr, $val, $fails){
                    $admin = Admin::get($val);
                    /*
                     | mengecek apakah admin ada dan
                     | admin yang diupdate adalah data admin
                     | sendiri
                     |
                     */
                    if(!$admin || $admin->id !== User::get()->id)
                        $fails("Anda tidak mempunyai akses ini.");
                }
            ],


            /**
             * \App\Model\InformasiAdmin
             * 
             * @property nama
             * @property nip
             */
            "nama"      => "min:4",
            "nip"       => "min:18|max:18"
        ];

        $messages = [
            "min"       => "Panjang :attribute setidaknya :min karakter",
            "max"       => "Panjang :attribute tidak boleh lebih dari :max karakter",
        ];

        return Validator::make(
            $request->all(),
            $rule,
            $messages,
        );
    }

    /**
     * method untuk mengupdate data admin oleh admin
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
    function update(Request $request, $id = null){

        if($id == null)
            /*
             | jika id kosong artinya admin mengedit datanya
             | sendiri.
             |
             */
            $id = User::get()->id;

        if(User::get()->level)
            /*
             | jika yang sedang login ternyata super admin maka
             | melakukan update melalui
             | App\Http\Controllers\SuperAdmin\AdminController
             | agar mendapat akses penuh seperti perubahan status
             | hingga level
             |
             */
            return self::updateSuperAdmin($request, $id);

        $request->request->add([ "id" => $id ]);

    	$validator = self::updateValidate($request);

    	if($validator->fails())
    		return $this->res($validator->fails(), null, null, $validator->errors());

    	$data = self::getAdminProps($validator);
        $info = self::getInfoAdminProps($validator);


    	/**
    	 * @var result_admin 				mengupdate data admin($id)
    	 * @var result_informasi_admin		menambahkan data informasi_admin($id) 
    	 */
    	$result_admin 			= Admin::update($data, $id);
    	$result_informasi_admin = InformasiAdmin::insert($info, Admin::get($id));

    	$result = $result_admin && $result_informasi_admin;

		return parent::res($result, Admin::get($id));
    }

    /**
     * mengupdate admin menggunakan controller SuperAdmin
     * 
     * @param Request $request
     * @param integer $id
     * 
     * @return Response
     */
    static function updateSuperAdmin(Request $request, $id = null){
        $updateController = new SuperAdminController;
        return $updateController->update($request, $id);
    }
}
