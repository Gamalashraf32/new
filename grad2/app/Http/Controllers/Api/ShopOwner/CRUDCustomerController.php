<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\showCustomerInfo;
use App\Http\Resources\ShowCustomerByID;
use Illuminate\Validation\Rule;

class CRUDCustomerController extends Controller
{
    use ResponseTrait;


    public function addcustomer(Request $request)
    {
        $shop_id =auth('shop_owner')->user()->shop()->first()->id;
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:3|max:255',
            'second_name' => 'required|string|min:3|max:255',
            'email' => [
                'required',
                Rule::unique('users', 'email')->where('shop_id' , $shop_id),
            ],
            'password' => 'required|confirmed',
            'phone_number' => [
                'required',
                Rule::unique('users','phone_number')->where('shop_id' , $shop_id),
            ],
            'address' => 'required',
            'city'=>'required'
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }

        $user=auth('shop_owner')->user();
        $shop_id=Shop::where('shop_owner_id',$user->id)->first();

        if (auth('shop_owner')->user()) {
            User::create([
                'first_name' => $request->first_name,
                'second_name' => $request->second_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'shop_id' => $shop_id->id,
                'city' => $request->city
            ]);
            return $this->returnSuccess('customer saved successfully', 200);
        } else {
            return $this->returnError('you are not authorized to edit this data', 401, false);
        }


    }

    public function showcustomer()
    {

        $shop_id = auth('shop_owner')->user()->shop()->value('id');

        $customers = User::where([['shop_id', $shop_id]])->get();

        if ($customers) {
            return $this->returnData('ok', $customers, 400);
        }
        return $this->returnError('Customers dose not exists', 404);
    }

    public function showcustomerwithid($id)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');

        $customers = User::where('shop_id', $shop_id)->find($id);

        if ($customers) {
            return $this->returnData('ok', new ShowCustomerByID($customers), 400);
        }
        return $this->returnError('Customers dose not exists', 404);
    }

    public function update(Request $request, $id)
    {

        $shop_id = auth('shop_owner')->user()->shop()->first();
        $validate = Validator::make($request->all(), [
            'email' => [Rule::unique('users', 'email')->where('shop_id' , $shop_id->id)->ignore($id)]
        ]);
        if ($validate->fails()) {
            $errors = [];
            foreach ($validate->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        $customer = User::where([['shop_id', $shop_id->id]])->first()->find($id);

        if (!$customer) {
            return $this->returnError('Customer dose not exists', 404);
        }
        $customer->update($request->all());
        if ($customer) {
            return $this->returnData('Product saved', $customer, 201);
        }
        return $this->returnError('Product not saved', 400);
    }


    public function delete($id)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');

        $customer = User::where('shop_id', $shop_id)->find($id);

        if (!$customer) {
            return $this->returnError('Customer dose not exists', 404);
        }
        $customer->delete($id);
        return $this->returnSuccess('Customer deleted', 200);
    }

    public function validator(Request $request)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $customer = User::where('shop_id', $shop_id)->where('email',$request->email)->first();
        if($customer)
        {
            return $this->returnSuccess('Customer found', 200);
        }
        else{
            return $this->returnError('Customer not found', 400);
        }
    }
}
