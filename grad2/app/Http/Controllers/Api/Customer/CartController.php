<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Api\ShopOwner\OrderController;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartProducts;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    use ResponseTrait;
    public function add_product(Request $request)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $user_id = auth('api')->user()->id;
        $cart = Cart::where('shop_user_id', $user_id)->first();
        if (!$cart) {
            $cart = Cart::create([
                'shop_id' => $shop_id,
                'shop_user_id' => $user_id
            ]);
        }
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'quantity' => 'required',
        ]);
        if($validate->fails())
        {
            $errors=[];
            foreach($validate->errors()->getMessages() as $message)
            {
                $error=implode($message);
                $errors[]=$error;
            }
            return $this->returnError(implode(' , ',$errors),400);
        }

        $product=Product::where('shop_id',$shop_id)->where('name',$request->name)->first();
        if(is_null($product))
        {
            return $this->returnError($request->name." not found",400);
        }
        if((new OrderController)->check_quantity($request->variant1, $request->variant2, $product->id, $request->quantity))
        {
            return $this->returnError($request->name." not found in stock",400);
        }
        $variant_id2=ProductVariant::where('product_id',$product->id)->where('value',$request->variant2)->first();
        $cartproduct=CartProducts::create([
            'shop_id'=> $shop_id,
            'shop_user_id'=> $user_id,
            'cart_id'=> $cart->id,
            'product_id'=> $product->id,
            'product_name'=> $request->name,
            'quantity' => $request->quantity,
            'variant1' => $request->variant1,
            'price' => $product->price
        ]);
        if(!is_null($variant_id2))
        {
            $cartproduct->variant2 = $request->variant1;
        }
        $cart->increment('subtotal_price', $cartproduct->quantity * $cartproduct->price);
        return $this->returnSuccess("Product Added",200);
    }

    public function delete_product($id)
    {
        $product = CartProducts::find($id);
        $cart=Cart::where('id',$product->cart_id)->first();
        $cart->decrement('subtotal_price', $product->quantity * $product->price);
        $product->delete($id);
        return $this->returnSuccess("Product deleted", 200);
    }
    public function update_product(Request $request,$id)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $user_id = auth('api')->user()->id;
        $cart=Cart::where('shop_id',$shop_id)->where('shop_user_id',$user_id)->first();
        $product = CartProducts::where('id',$id)->first();
        $cart->decrement('subtotal_price', $product->quantity * $product->price);
        $cart->increment('subtotal_price', $request->quantity * $product->price);
        $product->update($request->all());
        return $this->returnSuccess("Product updated", 200);
    }
    public function show(Request $request)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $user_id = auth('api')->user()->id;
        $cart=Cart::where('shop_id',$shop_id)->where('shop_user_id',$user_id)->first();
        if($cart->subtotal_price>0) {
            $cartproducts = CartProducts::where('cart_id', $cart->id)->get();
            $data = [
                'subtotal_price' => $cart->subtotal_price,
                'Products' => $cartproducts
            ];
            return $this->returnSuccess($data, 200);
        }
        else {
            return $this->returnError("No Products added",400);
        }
    }
    public function place_order(Request $request)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $user = auth('api')->user();
        $cart=Cart::where('shop_id',$shop_id)->where('shop_user_id',$user->id)->first();
        if($cart) {
            $products = CartProducts::where('cart_id', $cart->value('id'))->get();
            $data = [
                'email' => $user->email,
                'shop_id' => $shop_id,
                'note' => $request->note,
                'discounts' => $request->discounts,
                'products' => $products
            ];
            $request_order = new Request($data);
            $order = (new OrderController)->add_order($request_order);
            $cart->delete($cart->id);
            return $this->returnSuccess($order, 200);
        }
        else
        {
            return $this->returnError("no products in the cart",400);
        }
    }
}
