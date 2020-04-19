<?php

namespace App\Generator;

use \Firebase\JWT\JWT as FJWT;
use \Firebase\JWT\ExpiredException;

class JWT {

	public static $default_key = "__edd";
	public static $default_time = 60;

    public static function make($data, $time = null){
    	if(isset($time))
			FJWT::$leeway = $time ?? self::$default_time; // $leeway in seconds


    	$key = env("JWT_KEY", self::$default_key);

		$token = FJWT::encode($data, $key);



    	return collect( [
    		"status" 	=> $token ? true : false,
    		"token" 	=> $token
    	] );
    }

    public static function payload($type, $request, $id, $level = 0){
    	return collect([
		    "typ" 	=> $type,
		    "ori" 	=> $request,
		    "id" 	=> $id,
		    "lev" 	=> $level,
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

			$decoded 	= FJWT::decode($token, $key, array('HS256'));
			$status		= true;
			$message 	= "✔";
		
		} catch (ExpiredException $e) {

			$message 	= $e->getMessage();
			$status		= false;
			$expired 	= true;

		} catch (\Exception $e) {

			$message 	= $e->getMessage();
			$status		= false;

		}

		return collect([
			"data" 		=> $decoded,
			"status" 	=> $status,
			"message"	=> $message,
			"expired"	=> $expired,
		]);
    }
}
