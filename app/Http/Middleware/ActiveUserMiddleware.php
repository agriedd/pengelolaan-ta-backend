<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\{
    User,
    Admin as ModelAdmin,

    Exceptions\CustomHandler,
};

class ActiveUserMiddleware
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
     * 
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
     * 
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null){
        /*
         | middleware untuk mengecek user berstatus aktif
         | 
         */
        $user = User::get($request);

        if(!$user || $user->status === 0 || $user->status !== null){
            /*
             | jika user dengan kolom status sama dengan 1 (satu) saja
             | atau user tanpa kolom status saja yang dapat melanjutkan
             |
             */
            return response()->json(CustomHandler::unauthorized());
        }
        return $next($request);
    }
}
