<?php

namespace App\Http\Middleware;

use App\Models\ShopOwner;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;

class Discountcodes
{
    use ResponseTrait;
    public function handle(Request $request, Closure $next)
    {
        $shop_owner_id=auth('shop_owner')->user()->first()->id;
        $shop_owner=ShopOwner::where('id',$shop_owner_id)->first()->plan_id;
        if(is_null($shop_owner) || $shop_owner == 1 || $shop_owner == 2)
        {
            return  $this -> returnError('you cant add Discount codes, please upgrade your plan',400);
        }
        else {
            return $next($request);
        }
    }
}
