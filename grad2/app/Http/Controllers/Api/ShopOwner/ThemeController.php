<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ThemeController extends Controller
{
    use  ResponseTrait;

    public function chooseTheme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'theme_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        $user = auth('shop_owner')->user();
        $user->theme_id = $request->theme_id;
        $user->save();
        return $this->returnSuccess('theme saved successfully', 200);
    }
}
