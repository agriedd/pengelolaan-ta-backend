<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\{
    User,
    Repository\AdminRepository as Admin,

    Exceptions\CustomHandler,
    Generator\JWT,
};

class SuperAdminMiddleware
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
        // if ($this->auth->guard($guard)->guest()) {

        if( $token = $this->getToken($request) ) {
            
            $jwt = $this->checkToken($token);

            if( !$jwt->get("status") && $jwt->get("expired") ){
                return response()->json(CustomHandler::tokenExpired());
            } elseif( !$jwt->get("status") ){
                return response()->json(CustomHandler::unauthorized());
            }

        } else {
            return response()->json(CustomHandler::unauthorized());
        }
        // }

        return $next($request);
    }

    public function getToken($request){
        return $request->BearerToken() ?? $request->_token;
    }

    public function checkToken($token){
        $data = JWT::decode($token);

        if(!$data->get("status")){
            return $data;
        }

        $encoded = collect($data->get("data"));
        switch ($encoded->get("typ")) {
            case User::ADMIN :
                /**
                 * jika akun admin sudah dinonaktifkan atau tidak ada
                 * 
                 * @todo cek token admin yang tersimpan pada riwayat login
                 */
                $admin = Admin::get($encoded->get("id"));

                if(!$admin || $admin->status === 0){
                    $data->put("status", false);
                }
                break;
            default:
                $data->put("status", false);
                break;
        }

        return $data;
    }
}
