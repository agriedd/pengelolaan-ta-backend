<?php

namespace App\Http\Middleware;

use Closure;
use App\{
    Model\Admin,
    Exceptions\CustomHandler,
};
use Auth;

class AdminMiddleware
{
    protected $auth;

    public function __construct(Auth $auth){
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
        if(!Auth::user() instanceof Admin)
            return response()->json(CustomHandler::unauthorized());

        // Post-Middleware Action

        return $next($request);
    }
}
