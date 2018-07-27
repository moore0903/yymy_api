<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

class RedisCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $self = $_SERVER['REQUEST_URI'];
        $token = $request->t;
        $post = $request->p;
        $redisValue = Redis::get($self.'|'.$token.'|'.$post);
        $content = $redisValue;
        if(empty($redisValue)){
            $response =  $next($request);
            $content = $response->content();
            Redis::set($self.'|'.$token.'|'.$post,$content);
            Redis::expire($self.'|'.$token.'|'.$post,7200);
        }
        return response($content,'200')
            ->header('Content-Type', 'application/json');
    }
}
