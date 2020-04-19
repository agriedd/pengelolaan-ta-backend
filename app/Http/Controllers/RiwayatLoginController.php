<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\RiwayatLoginRepository as Riwayat;

class RiwayatLoginController extends Controller
{
    function getAll(Request $request)
    {
    	$data = Riwayat::getAll($request);
    	return parent::res( true, $data );
    }
}
