<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class ShopSwitch
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
        $shop_id = $request->input('shop_id', 0);
        if (1 > $shop_id && -1 != $shop_id) {
            $shop_id = $request->header('shop_id');
        }
        Cache::store('array')->put('shop_id', $shop_id);
        return $next($request);
    }
}
