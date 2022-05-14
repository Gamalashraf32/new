<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Shop;
use App\Models\ShopOwner;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShopOwnerInfoController extends Controller
{
    use ResponseTrait;
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:3|max:255',
            'second_name' => 'required|string|min:3|max:255',
            'email' => [
                'required',
                Rule::unique('shop_owners','email')->ignore(auth('shop_owner')->user()->id),
            ],
            'password' => 'required|confirmed',
            'phone_number' => [
                'required',
                Rule::unique('shop_owners','phone_number')->ignore(auth('shop_owner')->user()->id),
            ],
            'site_name' => [
                'required',
                Rule::unique('shop_owners','site_name')->ignore(auth('shop_owner')->user()->id),
            ],
            'site_address' => 'required',
            'country' => 'required|string|min:3',
            'government' => 'required|string|min:3',
            'city' => 'required|string|min:3',
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
        $user->first_name = $request->first_name;
        $user->second_name = $request->second_name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->country = $request->country;
        $user->government = $request->government;
        $user->city = $request->city;
        $user->save();
        return $this->returnSuccess('your data updated successfully', 200);
    }

}
