<?php

namespace App\Repository;

use App\Model\RiwayatLogin;
use Carbon\Carbon;

class RiwayatLoginRepository extends Repository
{
	public static function model(){
		return new RiwayatLogin;
	}

	/**
	 * @method model
	 * mengambil Riwayat Login dengan sebuah parameter id
	 * 
	 * @param id
	 * 
	 * @return self | Model\RiwayatLogin
	 * 
	 */
	public static function get($id = null){
		if($id)
			return self::model()->find($id);
		else
			return new self();
	}

	public static function getAll($request){
		return self::model()->paginate($request->has("limit") ?? 10);
	}

	public static function insert($status, $request, $collection, $user = null){
		$collection = collect($request->all());

		$collection
			->put("ip_address", $request->ip())
			->put("headers", json_encode($request->header()))
			->put("status", $status ? "1" : "0")
			->put("created_at", Carbon::now())
			->put("updated_at", Carbon::now());

		if($status)

			return $user->riwayat()->insert( $collection->only([
				"username", "ip_address", "headers", "status", "created_at", "updated_at", "token"
			])->all() );

		else

			return self::model()->insert( $collection->only([
				"username", "ip_address", "headers", "status", "created_at", "updated_at"
			])->all() );
	}
}
