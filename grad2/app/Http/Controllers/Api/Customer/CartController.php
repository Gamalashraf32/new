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
        foreach ($request['products'] as $product) {
            $myproduct = Product::where('shop_id', $shop_id)->where('name', $product->name)->first();
            if (is_null($myproduct)) {
                return $this->returnError($product->name . " not found", 400);
            }
            if ((new OrderController)->check_quantity($product->variant1, $product->variant2, $myproduct->id, $product->quantity)) {
                return $this->returnError($product->name . " not found in stock", 400);
            }
            $variant_id2 = ProductVariant::where('product_id', $myproduct->id)->where('value', $product->variant2)->first();
            $cartproduct = CartProducts::create([
                'shop_id' => $shop_id,
                'shop_user_id' => $user_id,
                'cart_id' => $cart->id,
                'product_id' => $myproduct->id,
                'product_name' => $product->name,
                'quantity' => $product->quantity,
                'variant1' => $product->variant1,
                'price' => $myproduct->price
            ]);
            if (!is_null($variant_id2)) {
                $cartproduct->variant2 = $request->variant1;
            }
            $cart->increment('subtotal_price', $cartproduct->quantity * $cartproduct->price);
            return $this->returnSuccess("Product Added", 200);
        }
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
