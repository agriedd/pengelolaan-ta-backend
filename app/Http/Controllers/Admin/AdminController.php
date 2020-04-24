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
use Validator;
use Auth;


class AdminController extends Controller
{
    /**
     * 
     */
    function __construct(){
        $this->middleware("auth:admin");
        // $this->middleware("active");
        // $this->middleware("admin");
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
        return collect($validator->getData())->only([ "username", "password" ]);
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
        return collect($validator->getData())->only([ "nama", "nip" ]);
    }

    static function updateValidate(Request $request)
    {
        return Validator::make(
            $request->all(),
            [

                /**
                 * @property admin \App\Model\Admin
                 * 
                 */
                "username"  => [
                    "min:6",
                    function($attr, $val, $fail) use($request){

                        if(Admin::getByUsernameExcept($val, $request->id))
                            $fail("{$attr} `{$val}` sudah digunakan.");
                        
                    }
                ],
                "password"  => "min:6",
                "id"    => [
                    function($attr, $val, $fails){
                        $admin = Admin::get($val);
                        if(!$admin || $admin->id !== Auth::user()->id)
                            $fails("Anda tidak mempunyai akses ini.");
                    }
                ],


                /**
                 * @property informasi admin \App\Model\InformasiAdmin
                 * 
                 */
                "nama"      => "min:4",
                "nip"       => "min:18|max:18"
            ],
            [
                "min"       => "Panjang :attribute setidaknya :min karakter",
                "max"       => "Panjang :attribute tidak boleh lebih dari :max karakter",
            ]
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
            $id = Auth::user()->id;

        if(Auth::user()->level)
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
    	 * 
    	 */
    	$result_admin 			= Admin::update($data, $id);
    	$result_informasi_admin = InformasiAdmin::insert($info, Admin::get($id));

    	$result = $result_admin && $result_informasi_admin;

		return parent::res($result, Admin::get($id));
    }

    static function updateSuperAdmin(Request $request, $id = null){
        $updateController = new SuperAdminController;
        return $updateController->update($request, $id);
    }
}
