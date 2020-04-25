<?php

namespace App\Generator;

use \Firebase\JWT\JWT as FJWT;
use \Firebase\JWT\ExpiredException;

class JWT {

	public static $default_key = "__edd";
	public static $default_time = 60;

    public static function make($data, $time = null){
    	if(isset($time))
    		/*
    		 | mengatur waktu expired pada token berdasarkan
    		 | detik
    		 |
    		 */
			FJWT::$leeway = $time ?? self::$default_time;

    	$key = env("JWT_KEY", self::$default_key);
    	/*
    	 | menggunakan key yang ada pada .env atau $default_key
    	 |
    	 */
		$token = FJWT::encode($data, $key);

    	return collect([
    		"status" 	=> $token ? true : false,
    		"token" 	=> $token
    	]);
    }

    public static function payload($type, $request, $user){
    	return collect([
		    "typ" 	=> $type,
		    "ori" 	=> $request,
		    "id" 	=> $user->id,
		    "lev" 	=> $user->level ? 1 : 0,
		    "tim"	=> \Carbon\Carbon::now()->getTimestamp()
    	]);
    }

    public static function decode($token){
 
    	$key = env("JWT_KEY", self::$default_key);

    	$decoded = null;
    	$message = "❌";
    	$status = false;
    	$expired = false;

		try {
			/*
			 | jika berhasil
			 |
			 */
			$decoded 	= FJWT::decode($token, $key, array('HS256'));
			$status		= true;
			$message 	= "✔";
		
		} catch (ExpiredException $e) {
			/*
			 | jika gagal akibat token expired
			 |
			 */
			$message 	= $e->getMessage();
			$status		= false;
			$expired 	= true;

		} catch (\Exception $e) {
			/*
			 | jika gagal mendecode token
			 |
			 */
			$message 	= $e->getMessage();
			$status		= false;
		}

		return collect([
			"data" 		=> collect($decoded),
			"status" 	=> $status,
			"message"	=> $message,
			"expired"	=> $expired,
		]);
    }
}
