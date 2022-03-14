<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    use  ResponseTrait;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
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
        if (!$token = auth('admin')->attempt($validator->validated())) {
            return $this->returnError(__('auth.failed'), 400);
        }

        return $this->returnData("Here is a valid token",
            [
                'token' => $token,
                'token_type' => 'bearer',
            ],
            200);
    }

}
