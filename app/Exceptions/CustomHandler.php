<?php

namespace App\Exceptions;

class CustomHandler
{
	/**
	 * menghandle http error
	 * 
	 * @param Request $request
	 * @param Exception $exception
	 * @param Closure $default akan dipanggil pada keadaan tertentu
	 * 
	 * @return Response
	 */
	public static function http($request, $exception, $default){
		if(env("APP_DEBUG"))
			/*
			 | jika sedang dalam development mode atau saat
			 | debug diaktifkan maka nilai defaultnya yang direturn
			 |
			 */
			return self::http_debug($request, $exception, $default);
		/*
		 | jika tidak dalam mode debug maka method custom_http
		 | yang akan direturn
		 |
		 */
		return self::custom_http($request, $exception, $default);
	}

	/**
	 * mereturn response default
	 * 
	 * @param Request $request
	 * @param Exception $exception
	 * @param Closure $default
	 * 
	 * @return Closure
	 */
	public static function http_debug($request, $exception, $default){
		return $default;
	}

	/**
	 * custom http respose
	 * 
	 * @param Request $request
	 * @param Exception $exception
	 * @param Closure $default
	 * 
	 * @return Response JSON
	 */
	public static function custom_http($request, $exception, $default){
		switch ($exception->getStatusCode()) {
			case 404:
				/*
				 | Jika Status Code sama dengan 404
				 |
				 */
				return response()->json(
					self::format("url {$request->getUri()} tidak ditemukan", $exception),
					$exception->getStatusCode()
				);
				break;
			default:
				/*
				 | respon dinamis
				 |
				 */
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