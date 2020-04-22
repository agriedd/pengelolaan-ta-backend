<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\{
	AdminRepository as Admin,
	InformasiAdminRepository as InformasiAdmin,
};
use Validator;
use Auth;
use Illuminate\Support\Collection;

class AdminController extends Controller
{
	

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

}
