<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\gamal;

class CRUDCustomerController extends Controller
{
    use ResponseTrait;


    public function addcustomer(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'second_name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'phone_number' => 'required',
            'address' => 'required',

        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }

        if (auth('shop_owner')->user()) {
            User::create([
                'first_name' => $request->first_name,
                'second_name' => $request->second_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'shop_id' => $request->shop_id,
            ]);
            return $this->returnSuccess('customer saved successfully', 200);
        } else {
            return $this->returnError('you are not authorized to edit this data', 401, false);
        }


    }

    public function showcustomer()
    {
        $customers = User::all();

        if ($customers) {
            return $this->returnData('ok', new gamal($customers), 400);
        }
        return $this->returnError('Customers dose not exists', 404);
    }


    public function update(Request $request, $id)
    {

        $customer = User::find($id);
        if (!$customer) {
            return $this->returnError('Customer dose not exists', 404);
        }
        $customer->update($request->all());
        if ($customer) {
            return $this->returnData('Product saved', new gamal($customer), 201);
        }
        return $this->returnError('Product not saved', 400);
    }


    public function delete($id)
    {
        $customer = User::find($id);
        if (!$customer) {
            return $this->returnError('Customer dose not exists', 404);
        }
        $customer->delete($id);
        return $this->returnSuccess('Customer deleted', 200);
    }
}

