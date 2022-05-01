<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\ShopOwner;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;

class NoCategories
{
    use ResponseTrait;
    public function handle(Request $request, Closure $next)
    {
        $shop_id=auth('shop_owner')->user()->shop()->first()->id;
        $shop_owner_id=auth('shop_owner')->user()->first()->id;
        $shop_owner=ShopOwner::where('id',$shop_owner_id)->first()->plan_id;
        $category=Category::where('shop_id',$shop_id)->get()->count();
        if(is_null($shop_owner))
        {
            if($category<=2)
            {
                return $next($request);
            }
            else{
                return  $this -> returnError('you cant add more categories please upgrade your plan',400);
            }
        }
        else {
            if($shop_owner == 1){
                if($category<=5)
                {
                    return $next($request);
                }
                else{
                    return  $this -> returnError('you cant add more categories please upgrade your plan',400);
                }
            }
            elseif ($shop_owner == 2){
                if($category<=10)
                {
                    return $next($request);
                }
                else{
                    return  $this -> returnError('you cant add more categories please upgrade your plan',400);
                }
            }
            else{
                return $next($request);
            }
        }

    }
}
