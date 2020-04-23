<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\DosenRepository as Dosen;

class DosenController extends Controller
{
    function getByUsername(Request $request, $username){
    	return parent::res(true, Dosen::getByUsername($username));
    }
}
