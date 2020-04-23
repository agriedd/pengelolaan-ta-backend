<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\{
    User,
    Model\Admin as ModelAdmin,
    Repository\AdminRepository as Admin,

    Exceptions\CustomHandler,
};

class OnlyAdminMiddleware
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
        /**
         * pengecekan apakah admin bersangkutan adalah admin super
         * 
         * @todo cast pada model admin mungkin merubah type data ke boolean
         * 
         */

        if(app("auth")->user() instanceof ModelAdmin && !app("auth")->user()->level){
            return $next($request);
        }

        return response()->json(CustomHandler::unauthorized());
    }
}
