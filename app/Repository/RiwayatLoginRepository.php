<?php

namespace App\Repository;

use App\Model\RiwayatLogin;
use Carbon\Carbon;

class RiwayatLoginRepository extends Repository
{

	const EXPIRED_LONG = 1; //weeks

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

		$collection = $collection->merge(collect($request->all()));


		$collection
			->put("ip_address", $request->ip())
			->put("headers", json_encode($request->header()))
			->put("status", $status ? "1" : "0")
			->put("created_at", Carbon::now())
			->put("updated_at", Carbon::now())

			->put("expired_at", self::expiredDate());

		if($status){
			return $user->riwayat()->create( $collection->only([
				"username", "ip_address", "headers", "status", "created_at", "updated_at", "token", "expired_at"
			])->all() );
		}
		else

			return self::model()->insert( $collection->only([
				"username", "ip_address", "headers", "status", "created_at", "updated_at"
			])->all() );
	}

	public static function expiredDate(){
		$now = Carbon::now();
		return $now->addWeeks( env("JWT_LONG", self::EXPIRED_LONG) );
	}

	public static function updateExpiredDate(RiwayatLogin $riwayat){
		return $riwayat->update( [ "expired_at" => self::expiredDate(), "updated_at" => Carbon::now() ] );
	}
}
