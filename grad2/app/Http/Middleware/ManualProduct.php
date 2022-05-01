<?php

namespace App\Http\Middleware;

use App\Models\ShopOwner;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;

class ManualProduct
{
    use ResponseTrait;

    public function handle(Request $request, Closure $next)
    {
        $shop_owner_id=auth('shop_owner')->user()->first()->id;
        $shop_owner=ShopOwner::where('id',$shop_owner_id)->first()->plan_id;
        if(is_null($shop_owner) || $shop_owner == 1)
        {
                return  $this -> returnError('you cant add order manually, please upgrade your plan',400);
        }
        else {
                return $next($request);
        }
    }
}
