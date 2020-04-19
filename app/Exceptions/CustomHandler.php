<?php

namespace App\Exceptions;

class CustomHandler
{

	public static function http($request, $exception, $default){
		if(env("APP_DEBUG"))
			return self::http_debug($request, $exception, $default);
		return self::custom_http($request, $exception, $default);
	}

	public static function http_debug($request, $exception, $default){
		return $default;
	}

	public static function custom_http($request, $exception, $default){
		switch ($exception->getStatusCode()) {
			case 404:
				return response()->json(
					self::format("url {$request->getUri()} tidak ditemukan", $exception),
					$exception->getStatusCode()
				);
				break;
			default:
				return response()->json(
					self::format("terjadi sebuah kesalahan.", $exception),
					$exception->getStatusCode()
				);
				break;
		}
	}

	public static function unauthorized(){
		return [
            "error"     => true,
            "message"   => "unauthorized",
            "errorCode" => 401,
        ];
	}
	public static function tokenExpired(){
		return [
            "error"     => true,
            "message"   => "token expired",
            "errorCode" => 401,
        ];
	}

	public static function query($request, $exception){
		return response()->json(
			self::format("terjadi sebuah kesalahan.", $exception),
			$exception->getStatusCode()
		);
	}

	public static function format($message = "", $exception){
		return [
            "error"     => true,
            "message"   => $message,
            "errorCode" => $exception->getStatusCode(),
        ];
	}
}