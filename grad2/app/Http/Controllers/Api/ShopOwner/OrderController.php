<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Api\ShopOwner\DiscountCodeController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Shipping;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ResponseTrait;
    public function add_order(Request $request)
    {
        if(is_null($request->shop_id))
        {
            $shop_id=auth('shop_owner')->user()->shop()->first()->id;
        }
        else
        {
            $shop_id = $request->shop_id;
        }
        foreach ($request['products'] as $product_order)
        {
            $product_id  = Product::where('shop_id',$shop_id)->where('name',$product_order['product_name'])->value('id');

            if(is_null($product_id))
            {
                return $this->returnError($product_order['product_name']." not found",400);
            }
            if($this->check_quantity($product_order['variant1'], $product_order['variant2'], $product_id, $product_order['quantity']))
            {
                return $this->returnError($product_order['product_name']." not found in stock",400);
            }
        }
        try {
            DB::transaction(function () use ($request) {
                if(is_null($request->shop_id))
                {
                    $shop_id=auth('shop_owner')->user()->shop()->first()->id;
                }
                else
                {
                    $shop_id = $request->shop_id;
                }
                $user_id = User::where('shop_id',$shop_id)->where('email', $request->email)->value('id');
                $order = Order::create([
                    'shop_id'=>$shop_id,
                    'shop_user_id' => $user_id,
                    'note' => $request->note,
                ]);
                foreach ($request['products'] as $product_order) {
                    $product_id = Product::where('name', $product_order['product_name'])->value('id');
                    $category= Category::where('id',Product::where('name', $product_order['product_name'])
                        ->value('category_id'))->first()->extra_shipping;
                    $order->increment('extra_shipping', $category);
                    $product_variant1 = ProductVariant::where('product_id', $product_id)
                        ->where('value', $product_order['variant1'])->first();
                    $product_variant2=ProductVariant::where('product_id',$product_id)
                        ->where('value',$product_order['variant2'])->first();
                    $order_product = OrderProduct::create([
                        'shop_id' => $shop_id,
                        'order_id' => $order->id,
                        'product_id' => $product_id,
                        'variant1' => $product_variant1->value,
                        'name' => $product_order['product_name'],
                        'quantity' => $product_order['quantity'],
                        'price' => Product::where('id', $product_id)->value('price')
                    ]);
                    ProductVariant::find($product_variant1->id)->decrement('quantity', $product_order['quantity']);
                    if($product_variant2)
                    {
                        $order_product->variant2 = $product_variant2->value;
                        ProductVariant::find($product_variant2->id)->decrement('quantity', $product_order['quantity']);

                    }
                    $order->increment('subtotal_price', $order_product->quantity * $order_product->price);
                }
                $user = User::where('email', $request->email)->first();
                $ship_price= Shipping::where('shop_id',$shop_id)->where('government',$user->city)->value('price');
                if(!is_null($request->discounts))
                {
                    $dis =(new DiscountCodeController)->calculate_discount($request->discounts,$order->subtotal_price);
                    if(is_int($dis))
                    {
                        $order->increment('discounts', $dis);
                    }
                    else
                    {
                         throw new \ErrorException($dis);
                    }
                }
                $order->increment('shipping_price', $ship_price);
                $order->increment('total_price', ($order->subtotal_price + $ship_price + $order->extra_shipping) - $order->discounts);
                if($user->balance>0){
                    if($user->balance > $order->total_price){
                        $order->total_price = 0;
                        $user->decrement('balance',$order->total_price);
                        $order->increment('user_balance',$order->total_price);
                        $order->save();
                    }
                    else{
                        $order->decrement('total_price',$user->balance);
                        $order->increment('user_balance',$user->balance);
                        $user->balance = 0;
                        $order->save();
                        $user->save();
                    }
                }

            });
        }catch (\Exception $exception)
        {
            return $this->returnError("something is wrong, please try again",400);
        }
        return $this->returnSuccess("Order Placed",200);
    }


    public function delete_order($id)
    {
        $shop_id =auth('shop_owner')->user()->shop()->first()->id;
        $shop_id_order=Order::where('id',$id)->value('shop_id');
        if($shop_id==$shop_id_order) {
            $order = Order::find($id);
            if($order->status=='cancelled') {
                if (!$order) {
                    return $this->returnError("This Order not exist", 400);
                }
                $order->delete($id);
                return $this->returnSuccess("Order deleted", 200);
            }
            else
            {
                return $this->returnError("Order should be cancelled first", 400);
            }
        }
        else
        {
            return $this->returnError("You are not authorized", 401);
        }
    }

    public function showall_orders()
    {
        $shop_id=auth('shop_owner')->user()->shop()->first();
        $order=Order::where('shop_id',$shop_id->id)->get();
        if(!$order)
        {
            return $this->returnError("No Order exists",400);
        }
        return $this->returnData("Your Orders ",$order,200);
    }


    public function update($id ,Request $request)
    {
        $shop_id = auth('shop_owner')->user()->shop()->first()->id;
        $shop_id_order = Order::where('id', $id)->value('shop_id');
        $order = Order::find($id);
        if ($order) {
            if ($shop_id == $shop_id_order) {
                if ($request->status == 'cancelled') {
                    $products = OrderProduct::where('order_id', $id)->get();
                    foreach ($products as $product) {
                        $product_var1 = ProductVariant::where('product_id', $product->product_id)
                            ->where('value', $product->variant1)->first();
                        $product_var2 = ProductVariant::where('product_id', $product->product_id)
                            ->where('value', $product->variant2)->first();
                        if ($product_var1) {
                            $product_var1->increment('quantity', $product->quantity);
                        }
                        if ($product_var2) {
                            $product_var2->increment('quantity', $product->quantity);
                        }
                    }
                    $user = User::where('shop_user_id',$order->shop_user_id)->first();
                    $user->increment('balance',$order->user_balance);
                    $user->save();
                    $order->status = $request->status;
                    $order->save();
                    return $this->returnSuccess("Order cancelled", 200);
                } else {
                    if ($order->status != 'cancelled') {

                        $order->status = $request->status;
                        $order->save();
                        return $this->returnSuccess("Status updated", 200);
                    }
                    else
                    {
                        return $this->returnError("You can't updated it", 401);
                    }
                }
            } else {
                return $this->returnError("You are not authorized", 401);
            }
        }
        else {
                return $this->returnError("Order Not Found", 400);
            }
    }


    public function show_order($id)
    {
        $shop_id =auth('shop_owner')->user()->shop()->first()->id;
        $order=Order::where('id',$id)->first();
        if($order) {
            $shop_id_order = $order->shop_id;
            $o_products = OrderProduct::where('order_id', $id)->get();
            if ($shop_id == $shop_id_order) {
                $user_id = $order->shop_user_id;
                $user_email = User::where('id', $user_id)->value('email');
                $data = [
                    'User' => $user_email,
                    'status' => $order->status,
                    'note' => $order->note,
                    'subtotal_price' => $order->subtotal_price,
                    'discounts' => $order->discounts,
                    'shipping_price' => $order->shipping_price,
                    'total_price' => $order->total_price,
                    'Products' => $o_products
                ];
                return $this->returnData("Your Order ", $data, 200);
            } else {
                return $this->returnError("You are not authorized", 401);
            }
        }
        else{
            return $this->returnError("Order Not Found", 400);
        }
    }


    public function check_quantity($variant1,$variant2,$product_id,$quantity)
    {
        $product_variant1=ProductVariant::where('product_id',$product_id)
            ->where('value',$variant1)->first();
        if(!$product_variant1||$product_variant1->quantity<$quantity)
        {
            return true;
        }
        if(is_null($variant2))
        {
            return false;
        }
        else
        {
            $product_variant2=ProductVariant::where('product_id',$product_id)
                ->where('value',$variant2)->first();
            if(!$product_variant2||$product_variant2->quantity<$quantity)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }


    public function refund_status(Request $request,$id){
        $shop_id =auth('shop_owner')->user()->shop()->first()->id;
        $refund=Refund::where('shop_id',$shop_id)->find($id);
        if($refund){
            if($request->status == 'Reviewing' && ( $refund->status!='Refunded' || $refund->status!='Declined'))
            {
                $refund->status = $request->status;
                $refund->details = $request->details;
                return $this->returnSuccess("Order is under review", 200);
            }
            elseif ($request->status == 'Declined' && $refund->status!='Refunded'){
                $refund->status = $request->status;
                $refund->details = $request->details;
                return $this->returnError('Order cant be returned',400);
            }
            elseif ($request->status == 'Refunded'){
                $refund->status = $request->status;
                $refund->details = $request->details;
                $order = Order::where('id',$refund->order_id)->first();
                $order->status = 'Refunded';
                $products = OrderProduct::where('order_id', $id)->get();
                foreach ($products as $product) {
                    $product_var1 = ProductVariant::where('product_id', $product->product_id)
                        ->where('value', $product->variant1)->first();
                    $product_var2 = ProductVariant::where('product_id', $product->product_id)
                        ->where('value', $product->variant2)->first();
                    if ($product_var1) {
                        $product_var1->increment('quantity', $product->quantity);
                    }
                    if ($product_var2) {
                        $product_var2->increment('quantity', $product->quantity);
                    }
                }
                $order->save();
                $refund->save();
                $user = User::where('id',$order->shop_user_id)->first();
                $user->increment('balance', $order -> total_price - $order -> shipping_price - $order -> extra_shipping);
                $user->save();
                return $this->returnSuccess("Order refunded", 200);
            }
        }
        else{
            return $this->returnError('Order not found',404);
        }
    }

    public function show_refund()
    {
        $shop_id =auth('shop_owner')->user()->shop()->first()->id;
        $refunds=Refund::where('shop_id',$shop_id)->get();
        if($refunds) {
            $orders_list = array();
            foreach ($refunds as $refund) {
                $order = Order::where('id', $refund->order_id)->first();
                $orders_list[] = [
                    'order_details' => $order->makeHidden(["status"]),
                    'refund_details' => $refund
                ];
            }
            return $this->returnData("Your refunded orders",$orders_list,200);
        }
        return $this->returnError("No refunded orders found",404);
    }

}
