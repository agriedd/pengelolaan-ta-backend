<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

use App\{
    User,
    Repository\AdminRepository as Admin,
    Repository\RiwayatLoginRepository as Riwayat,

    Exceptions\CustomHandler,
    Generator\JWT,
};

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.


        $this->app['auth']->viaRequest('token', function ($request) {

            if($token = self::getToken($request)) {
            
                $jwt = self::checkToken($token, $request);

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

        });
    }

    public static function getToken($request){
        return $request->BearerToken() ?? $request->_token;
    }
        
    /**
     * pengecekan dan validasi token hingga aktivitas login
     * 
     * @todo pindahkan proses ke auth
     * @todo untuk filter user lain selain admin
     * 
     */
    public static function checkToken($token, $request){
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
