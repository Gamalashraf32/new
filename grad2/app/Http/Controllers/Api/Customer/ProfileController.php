<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\EditInfoOfUser;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Refund;
use App\Models\Shipping;
use App\Models\Shop;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function PHPUnit\Framework\isEmpty;

class ProfileController extends Controller
{
    use ResponseTrait;

    public function editinfo(Request $request)
    {
        $user = auth('api')->user();
        if(!$user)
        {
            return $this->returnError('Customer dose not exists', 404);
        }
        $user->update($request->all());
        if ($user) {
            return $this->returnSuccess('Customer saved', 201);
        }
        return $this->returnError('Customer not saved', 400);
    }

    public function showallorders(Request $request)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $user = auth('api')->user();
        $orders=Order::where('shop_id',$shop_id)->where('shop_user_id',$user->id)->get();
        if($orders) {
            $orders_list = array();
            foreach ($orders as $order){
                $o_products = OrderProduct::where('order_id', $order->id)->get();
                $orders_list[] = [
                    'id'=>$order->id,
                    'status' => $order->status,
                    'note' => $order->note,
                    'subtotal_price' => $order->subtotal_price,
                    'discounts' => $order->discounts,
                    'shipping_price' => $order->shipping_price,
                    'total_price' => $order->total_price,
                    'Products' => $o_products
                ];
            }
                return $this->returnData("Your Order ", $orders_list, 200);
        }
        else{
            return $this->returnError("Order Not Found", 400);
        }

    }


    public function showoneorder(Request $request,$id)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $user = auth('api')->user();
        $orders = Order::where('shop_id',$shop_id)->where('shop_user_id', $user->id)->find($id);
        if(isEmpty($orders))
        {
            return $this->returnData('Here are the orders',$orders,200);
        }
        return $this->returnError('No order here',404);

    }

    public function showcities(Request $request)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $ship= Shipping::where('shop_id', $shop_id)->get();
        if (!$ship) {
            return $this->returnError(' no shipping info yet', 404, true);
        }
        return $this->returnData('your shipping info is', $ship->makeHidden(["id","shop_id","updated_at","created_at","duration","price"]), 200);
    }

    public function create(Request $request,$id){
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $order=Order::find($id);
        if($order){
         Refund::create([
            'shop_id'=>$shop_id,
            'order_id'=>$id,
            'reason'=>$request->reason
        ]);
        return $this->returnSuccess("Refund is requested",200);
        }
        else{
            return $this->returnError("Order not found",404);
        }
    }

}
