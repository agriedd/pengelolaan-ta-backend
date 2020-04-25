<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\{
    User,
    Repository\AdminRepository as Admin,
    Repository\DosenRepository as Dosen,
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
    public function handle($request, Closure $next, $guard = null){

        if( $token = self::getToken($request) ){

            $data = self::checkToken($request);

            if($data->get("expired"))
                return response()->json(CustomHandler::tokenExpired());
            elseif(!$data->get("status"))
                return response()->json(CustomHandler::unauthorized());

            $encoded = $data->get("data");

            if($guard === User::ADMIN || User::ADMIN === $encoded->get("typ")){
                Admin::login($this->auth, $request, $token, $data);
            }
            if($guard === User::DOSEN || User::DOSEN === $encoded->get("typ")){
                Dosen::login($this->auth, $request, $token, $data);
            }
            if($guard === User::DOSEN || User::DOSEN === $encoded->get("typ") || $guard === null){

            }
            
            if($this->user())
                return $next($request);
        }
        return response()->json(CustomHandler::unauthorized());
    }

    public static function getToken($request){
        return $request->BearerToken() ?? $request->_token;
    }

    public static function checkToken($request){
        return JWT::decode(self::getToken($request));
    }
    public static function user(){
        return 
            app("auth")->guard("admin") ||
            app("auth")->guard("dosen") ||
            app("auth")->guard("mahasiswa");
    }
}
