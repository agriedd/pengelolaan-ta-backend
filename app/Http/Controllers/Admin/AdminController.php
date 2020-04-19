<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\{
	AdminRepository as Admin,
	InformasiAdminRepository as InformasiAdmin,
};
use Validator;
use Illuminate\Support\Collection;

class AdminController extends Controller
{
	function __construct(){
		//can middleware here
	}


	/**
	 * @method get JSON
	 * 
	 * @param $request Request
	 * @param $id
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
     * @method getAll JSON
     * 
     * @param $request
     * 
     * @return json { status: boolean, data: Array }
     * 
     * @todo add filter
     * 
     */
    function getAll(Request $request){
        dd($request);
    	$data 	= Admin::getAll();
    	$status = $data ? true : false;
    	return parent::res($status, $data);
    }


    /**
     * @method insert JSON
     * controller untuk menambah sebuah data admin baru
     * 
     * 
     * @param $request
     * 		@property username 		string, minimal 6 karakter dan maksimal 20 karakter
     * 		@property password 		string, minimal 6 karakter
     * 
     * @return json { status: boolean, error: bollean, data: Modal\Admin } 
     * 
     */
    function insert(Request $request){

    	$validator = Validator::make(
    		$request->all(),
    		[
	    		"username" => 
	    			[
		    			"required", "min:6",
		    			function($attribute, $value, $fail){
		    				if(Admin::get()->byUsername($value))
		    					$fail("{$attribute} `{$value}` sudah digunakan.");
		    			}
	    			],
    			"password" => "required|min:6",
    		],
    		[
	    		"required" 	=> "Membutuhkan :attribute",
	    		"min" 		=> "Panjang :attribute setidaknya :min karakter",
    		]
    	);

    	//jika validasi gagal

    	if($validator->fails())
    		return $this->res($validator->fails(), null, "", $validator->errors());

    	$result = Admin::insert( $request );
    	$status = $result ? true : false;

    	return parent::res($status, $result);
    }


    /**
     * @method update
     * method untuk mengupdate data admin oleh super admin
     * 
     * @param $request
     * @param $id
     * 
     * @return json {status: boolean, data: Model\Admin}
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
    		return $this->res($validator->fails(), null, "", $validator->errors());

    	$data = Collection::make($validator->getData());

    	$data_admin 			= Collection::make($data->only(["username", "password"]));
    	$data_informasi_admin 	= Collection::make($data->only(["nama", "nip", "level"]));

    	/**
    	 * @var result_admin 				mengupdate data admin($id)
    	 * @var result_informasi_admin		menambahkan data informasi_admin($id) 
    	 * 
    	 */
    	$result_admin 			= Admin::update($data_admin, $id);
    	$result_informasi_admin = InformasiAdmin::insert($data_informasi_admin, Admin::get($id));

    	$result = $result_admin && $result_informasi_admin;

		$this->res($result, null, "", $validator->errors());
    }
}
