<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    use ResponseTrait;

    public function login(Request $request)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $validator = Validator::make($request->all(), [
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
        $checker=User::where('shop_id',$shop_id)->where('email',$request->email)->first();
        $token = auth('api')->attempt(['email' => $request['email'], 'password' => $request['password']]);
        if (!$token || is_null($checker)) {
            return $this->returnError(__('auth.failed'), 400);
        }
        return $this->createNewToken($token);

    }

    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:3|max:255',
            'second_name' => 'required|string|min:3|max:255',
            'email' =>  [Rule::unique('users', 'email')
                ->where('shop_id' , $shop_id)],
            'password' => 'required|confirmed|min:8',
            'phone_number' => 'required|unique:users|min:11',
            'address'=>'required',
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


        User::create([
            'first_name' => $request->first_name,
            'second_name' => $request->second_name,
            'email' => $request->email,
            'password' => Hash::make($request['password']),
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'city'=>$request->city,
            'shop_id'=>$shop_id
        ]);

        $token = auth('api')->attempt(['email' => $request['email'], 'password' => $request['password']]);
        return $this->createNewToken($token);

    }

    public function profile()
    {
        if (auth('api')->user()) {
            return $this->returnData('customer- profile', auth('api')->user(), '200');
        } else {
            return $this->returnError('you are not authorized to show this data', 401, false);
        }
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout(true);
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
