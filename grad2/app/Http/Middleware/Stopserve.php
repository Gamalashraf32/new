<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use App\Models\ShopOwner;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class Stopserve
{
    use ResponseTrait;

    public function handle(Request $request, Closure $next)
    {
        $incoming_shop=$request->header('shop');
        $shop_owner=ShopOwner::where('site_name',$incoming_shop)->first();
        $exp_data=$shop_owner->expires_at;
        $now=Carbon::today()->toDateString();
        if(!is_null($exp_data)&&$exp_data->gte($now)) {
            return $next($request);
        }
        else
        {
            return  $this -> returnError('Shop Not working',400);
        }
    }
}
