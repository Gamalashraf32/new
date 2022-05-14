<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Shop;
use App\Models\ShopOwner;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShopOwnerController extends Controller
{
    use ResponseTrait;

    public function login(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        //Config::set('jwt.user', "App\Models\Admin");
        //Config::set('auth.guards.api.provider','admin');
//    Artisan::call('config:clear');
//        dd(\config('auth.guards.api.provider'));
        if (!$token = auth('shop_owner')->attempt($validator->validated())) {
            return $this->returnError(__('auth.failed'), 400);
        }

        return $this->createNewToken($token);
    }


    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:3|max:255',
            'second_name' => 'required|string|min:3|max:255',
            'email' =>  'required|email|unique:shop_owners,email',
            'password' => 'required|confirmed|min:8',
            'phone_number' => 'required|unique:shop_owners|min:11',
            'site_name' => 'required|string|min:3|unique:shop_owners',
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

        DB::beginTransaction();

        $shop_owner = ShopOwner::create([
            'first_name' => $request->first_name,
            'second_name' => $request->second_name,
            'email' => $request->email,
            'password' => Hash::make($request['password']),
            'phone_number' => $request->phone_number,
            'site_name' => $request->site_name,
            'site_address' => $request->site_address,
            'country' => $request->country,
            'government' => $request->government,
            'city' => $request->city,
        ]);

        Shop::create([
            'name' => $request->site_name,
            'shop_owner_id' => $shop_owner->id,
            'address'=> $request->site_address,
            'phone_number' => $request->phone_number,

        ]);

        DB::commit();
        $token = auth('shop_owner')->attempt(['email' => $request['email'], 'password' => $request['password']]);
        return $this->createNewToken($token);
    }

    public function profile()
    {

        if (auth('shop_owner')->user()) {
            return $this->returnData('shop_owner_info', auth('shop_owner')->user()->makeHidden([ "created_at","updated_at"]), '200');
        } else {
            return $this->returnError('you are not authorized to show this data', 401, false);
        }
    }

    public function logout(): JsonResponse
    {
        auth('shop_owner')->logout(true);
        return $this->returnSuccess(__('response.logged_out'), 200);
    }


    protected function createNewToken(string $token)
    {

        return $this->returnData("Here is a valid token",
            [
                'token' => $token,
                'token_type' => 'bearer',
            ],
            200);
    }

}
