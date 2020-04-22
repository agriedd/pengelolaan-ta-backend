<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\{
    User,
    Repository\AdminRepository as Admin,
    Repository\RiwayatLoginRepository as Riwayat,

    Exceptions\CustomHandler,
    Generator\JWT,
};

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        
        if( $token = $this->getToken($request) ) {
            
            $jwt = $this->checkToken($token, $request);

            if( !$jwt->get("status") && $jwt->get("expired") ){
                return response()->json(CustomHandler::tokenExpired());
            } elseif( !$jwt->get("status") ){
                if(Riwayat::getByToken($token))
                    return response()->json(CustomHandler::tokenExpired());

                return response()->json(CustomHandler::unauthorized());
            }

        } else {
            return response()->json(CustomHandler::unauthorized());
        }


        /**
         * @var update waktu kadaluarsa token +1 minggu
         */
        Riwayat::updateExpiredDate( $request->user->riwayatTerakhir );

        return $next($request);
    }

    public function getToken($request){
        return $request->BearerToken() ?? $request->_token;
    }


    /**
     * pengecekan dan validasi token hingga aktivitas login
     * 
     * @todo pindahkan proses ke auth
     * @todo untuk filter user lain selain admin
     * 
     */
    public function checkToken($token, $request){
        $data = JWT::decode($token);

        if(!$data->get("status")){
            return $data;
        }

        $encoded = collect($data->get("data"));
        $user = null;

        switch ($encoded->get("typ")) {
            case User::ADMIN :
                /**
                 * jika akun ada dan token terakhir sama dengan token yang dikirim dan token
                 * belum kadaluarsa
                 * 
                 * user dapat menggunakan token yang pernah terdaftar sebelum waktu expired 1 minggu
                 * setiap kali token digunakan maka waktu expired akan diperpanjang 1 minggu seterusnya
                 * setiap kali user melakukan login token baru akan diberi guna mencatat aktivitas
                 * atau riwayat login user
                 * 
                 */
                
                $user = Admin::getWithLastAuth($encoded->get("id"), $token);

                if(!$user || !$user->riwayatTerakhir){
                    $data->put("status", false);
                }
                break;
            default:
                $data->put("status", false);
                break;
        }

        //inject request
        $request->user = $user;
        $request->_token = $token;
        
        return $data;
    }
}
