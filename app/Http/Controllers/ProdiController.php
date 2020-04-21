<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\{
	ProdiRepository as Prodi,
};

class ProdiController extends Controller
{
	public function get(Request $request, $id)
	{
		return parent::res(true, Prodi::get($id));
	}
	public function getAll(Request $request)
	{
		return parent::res(true, Prodi::getAll($request));
	}
}
