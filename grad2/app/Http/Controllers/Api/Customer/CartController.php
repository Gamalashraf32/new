<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartProducts;
use App\Models\Product;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ResponseTrait;
    public function addToCart(Request $request)
    {
        $customer_id=auth('api')->user()->first();
        $cart_id=Cart::where('user_id',$customer_id->id)->fisrt();
        CartProducts::create([
            'cart_id' => $cart_id,
            'product_id'=> $request->product_id,

        ]);
    }
}
