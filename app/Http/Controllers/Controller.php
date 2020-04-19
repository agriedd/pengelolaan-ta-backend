<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
	public const SUCCESS = 'success';
	public const FAIL = 'fail';

    public function res($status = self::SUCCESS, $data = null, $message = null, $errors = null)
    {
    	if(is_bool($status))
    		$status = $status ? self::SUCCESS : self::FAIL;

    	if($status === self::SUCCESS)
    		return response()->json(
                self::format(true, false, $message ?? "✔", $data, $errors),
                200
            );

        return response()->json(
            self::format(false, true, $message ?? "❌", $data, $errors),
            200
        );
    }

    public static function format($status, $error, $message = null, $data = null, $errors = null){
        return [
            "status"        => $status,
            "error"         => $error,
            "message"       => $message ?? "",
            "errors"        => $errors,
            "data"          => $data
        ];
    }
}
