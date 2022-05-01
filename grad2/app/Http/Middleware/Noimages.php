<?php

namespace App\Http\Middleware;

use App\Models\ShopOwner;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;

class Noimages
{
    use ResponseTrait;

    public function handle(Request $request, Closure $next)
    {
        $noimages=count($request->images);
        $shop_owner_id=auth('shop_owner')->user()->first()->id;
        $shop_owner=ShopOwner::where('id',$shop_owner_id)->first()->plan_id;
        if(is_null($shop_owner))
        {
            if($noimages<=1)
            {
                return $next($request);
            }
            else{
                return  $this -> returnError('you cant add more images, please upgrade your plan',400);
            }
        }
        else {
            if($shop_owner == 1){
                if($noimages<=3)
                {
                    return $next($request);
                }
                else{
                    return  $this -> returnError('you cant add more images, please upgrade your plan',400);
                }
            }
            elseif ($shop_owner == 2){
                if($noimages<=5)
                {
                    return $next($request);
                }
                else{
                    return  $this -> returnError('you cant add more images, please upgrade your plan',400);
                }
            }
            else{
                return $next($request);
            }
        }
    }
}
