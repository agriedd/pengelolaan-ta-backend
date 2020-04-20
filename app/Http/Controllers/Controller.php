<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
	public const SUCCESS = 'success';
	public const FAIL = 'fail';


    /**
     * respose cepat untuk controller
     * 
     * @param string status
     * @param mixed data
     * @param string message
     * @param mixed errors
     * 
     * @return json
     * 
     */
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


    /**
     * format untuk respon data dari kontroler
     * 
     * @param boolean status        status request berhasil
     * @param boolean error         status error, wajib disetiap jenis respon
     * @param string message        pesan dalam string
     * @param mixed data            data yang diminta
     * @param mixed errors          jika pesan error melebihi 1
     * 
     */
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
