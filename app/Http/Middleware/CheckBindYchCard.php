<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\ApiResponse;

class CheckBindYchCard
{

    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user->ych_id) {
            return $this->error("用户未绑定会员卡", -90015);
        }

        return $next($request);
    }
}
