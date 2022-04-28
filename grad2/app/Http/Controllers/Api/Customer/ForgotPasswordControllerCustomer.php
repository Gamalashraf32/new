<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Mail\SendCodeResetPassword;
use App\Mail\SendCodeResetPasswordCustomer;
use App\Models\ResetCodePassword;
use App\Models\ResetCodePasswordCustomer;
use App\Models\ShopOwner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordControllerCustomer extends Controller
{
    public function invoke(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        // Delete all old code that user send before.
        ResetCodePasswordCustomer::where('email', $request->email)->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePasswordCustomer::create($data);

        // Send email to user
        Mail::to($request->email)->send(new SendCodeResetPasswordCustomer($codeData->code));

        return response(['message' => trans('passwords.sent')], 200);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_password_customers',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // find the code
        $passwordReset = ResetCodePasswordCustomer::firstWhere('code', $request->code);

        // check if it does not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return response(['message' => trans('passwords.code_is_expire')], 422);
        }

        // find user's email
        $user = User::firstWhere('email', $passwordReset->email);

        // update user password
        $user->update([
            'password'=>Hash::make($request->password)
        ]);
        // delete current code
        $passwordReset->delete();

        return response(['message' =>'password has been successfully reset'], 200);
    }
}
