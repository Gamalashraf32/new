<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartProducts;
use App\Models\Product;
use App\Models\ShopOwner;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ResponseTrait;
    public function get()
    {
        $shop_id=auth('api')->user()->shop();
        $shop_owner=ShopOwner::whereHas('shop',function ($query) use($shop_id)
        {
            $query->where('shop_id',$shop_id->id) ;
        })->first();
        dd($shop_owner->id);
    }
}
