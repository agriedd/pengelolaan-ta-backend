<?php

namespace App\Repository;

use App\Model\RiwayatLogin;
use Carbon\Carbon;

class RiwayatLoginRepository extends Repository
{

	/**
	 * @var integer dalam satuan minggu
	 * 
	 */
	const EXPIRED_LONG = 1;

	public static function model(){
		return new RiwayatLogin;
	}


	/**
	 * @method model
	 * mengambil Riwayat Login dengan sebuah parameter id
	 * 
	 * @param id
	 * 
	 * @return mixed
	 * 
	 */
	public static function get($id = null){
		if($id)
			return self::model()->find($id);
		else
			return new self();
	}


	/**
	 * mengambil semua riwayat admin user, pastikan hanya 
	 * super admin yang dapat mengakses
	 *
	 * @param Request $request
	 * 
	 * @return Array Collection
	 */
	public static function getAll($request){
		return self::model()->paginate($request->limit ?? 10);
	}


	/**
	 * mengambil riwayat login user tertentu
	 * 
	 * @param Request $request
	 * @param mixed $user
	 * 
	 * @return Array Collection
	 */
	public static function getByUser($request, $user){
		$data = $user->riwayat()
			->with(["user"])
			->latest()
			->paginate($request->limit ?? 10);
		return $data;
	}

	/**
	 * mengambil riwayat user berdasarkan token
	 * 
	 * @param string $token
	 */
	public static function getByToken($token){
		return self::model()
			->with(["user"])
			->where("token", $token)
			->first();
	}


	/**
	 * menambahkan data riwayat login
	 * 
	 * @param boolean $status status berhasil login
	 * @param Request $request
	 * @param mixed $user user yang sedang login
	 * 
	 * @return boolean
	 * 
	 * @todo hapus json_encode pada header jika casts pada model
	 * \App\Model\RiwayatLogin mempengaruhi method insert
	 */
	public static function insert($status, $request, $user = null){
		$collection = collect($request->all());
		$collection
			->put("ip_address", $request->ip())
			->put("headers", json_encode($request->header()))
			->put("status", $status ? "1" : "0")
			->put("created_at", Carbon::now())
			->put("updated_at", Carbon::now())

			->put("expired_at", self::expiredDate());

		if($user):
			/*
			 | jika terdapat user maka insert berdasarkan relasi user
			 |
			 */
			return $user->riwayat()->create(
				$collection->only([
					"username",
					"ip_address",
					"headers",
					"status",
					"created_at",
					"updated_at",
					"token",
					"expired_at",
				])->all()
			);
		else:
			/*
			 | jika tidak terdapat user maka insert tanpa relasi user
			 |
			 */
			return self::model()->insert(
				$collection->only([
					"username", 
					"ip_address", 
					"headers", 
					"status", 
					"created_at", 
					"updated_at"
				])->all() 
			);
		endif;
	}


	/**
	 * membuat tanggal expired untuk token
	 * 
	 * @return Carbon timestamps
	 */
	public static function expiredDate(){
		$now = Carbon::now();
		/*
		 | menambah beberapa minggu dari sekarang berdasarkan
		 | file .env atau const EXPIRED_LONG
		 |
		 */
		return $now->addWeeks( env("JWT_LONG", self::EXPIRED_LONG) );
	}


	/**
	 * mengupdate waktu kadaluarsa token
	 * 
	 * @param \App\Model\RiwayatLogin $riwayat
	 * 
	 * @return boolean
	 */
	public static function updateExpiredDate(RiwayatLogin $riwayat){
		return $riwayat->update([
			"expired_at" => self::expiredDate(),
			"updated_at" => Carbon::now() 
		]);
	}

	/**
	 * melakukan logout semua perangkat/token user
	 * atau token lain
	 * 
	 * @param mixed $user
	 * @param string $token
	 */
	public static function logoutAll($user, $tokenExcept = null){
		return $user->riwayat()
			->active()
			->success()
			->when($tokenExcept, function($query) use($tokenExcept){
				$query->where("token", "<>", $tokenExcept);
			})
			->update([ "expired_at" => Carbon::now() ]);
	}

	/**
	 * melakukan logout riwayat/token user tertentu
	 * 
	 * @param integer $id id dari App\Model\Riwayat
	 * @param mixed $user
	 */
	public static function logout($id, $user){
		return $user->riwayat()
			->active()
			->success()
			->find($id)
			->update([ "expired_at" => Carbon::now() ]);
	}
}
