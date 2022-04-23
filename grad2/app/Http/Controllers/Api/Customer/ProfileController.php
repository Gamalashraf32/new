<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\EditInfoOfUser;
use App\Models\Order;
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

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:3|max:255',
            'second_name' => 'required|string|min:3|max:255',
            'email' => [
                'required',
                Rule::unique('users','email')->ignore(auth('api')->user()->id),
            ],
            'password' => 'required|confirmed',
            'phone_number' => [
                'required',
                Rule::unique('users','phone_number')->ignore(auth('api')->user()->id),
            ],
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }


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

    public function showallorders()
    {
        $user = auth('api')->user();
        $orders = Order::where('shop_user_id', $user->id)->get();
        if(isEmpty($orders))
        {
            return $this->returnData('Here are the orders',$orders,200);
        }
        return $this->returnError('No order here',404);

    }


    public function showoneorder($id)
    {
        $user = auth('api')->user();
        $orders = Order::where('shop_user_id', $user->id)->find($id);
        if(isEmpty($orders))
        {
            return $this->returnData('Here are the orders',$orders,200);
        }
        return $this->returnError('No order here',404);

    }

}
