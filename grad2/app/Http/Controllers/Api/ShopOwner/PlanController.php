<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Api\PayMobController;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\ShopOwner;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    use ResponseTrait;
    public function show()
    {
       $plans= Plan::all();
       return $this->returnData('This is our all plans',$plans->makeHidden(["created_at","updated_at"]),200);
    }

    public function choose(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required',
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
        $plan = Plan::whereId($request->plan_id)->first();
        $paymob = new PayMobController();
        ShopOwner::where('id',$user->id)->update(['plan_id' => $request->plan_id, 'payment_status' => 'pending']);
        return $paymob->checkingOut($user->id, $plan->cost);


    }

    public function checkout_done() {
        $user=auth('shop_owner')->user();
        ShopOwner::where('id',$user->id)->update(['payment_status' => 'paid']); //,'expires_at' => Carbon::now()->addMonth(), 'is_active' => true]);
        return $this->returnSuccess('payment succeeded', 200);
    }
}
