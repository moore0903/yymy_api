<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
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
        if (Auth::guard($guard)->check()) {
            if($guard == 'system'){
                return redirect('/SystemManage/home');
            }elseif($guard == 'oneCardBusiness'){
                return redirect('/OneCardBusiness/BusinessInfo/home');
            }elseif($guard == 'appcms'){
                return redirect('/cms');
            }else{
                return redirect('/home');
            }
        }

        return $next($request);
    }
}
