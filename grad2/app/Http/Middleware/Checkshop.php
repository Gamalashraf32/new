<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;

class Checkshop
{
    use ResponseTrait;
    public function handle(Request $request, Closure $next)
    {
        $incoming_shop=$request->header('shop');
        $check=Shop::where('name',$incoming_shop)->first();
        if(is_null($check))
        {
            return  $this -> returnError('Shop Not Found',404);
        }
        return $next($request);
    }
}
