<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Api\PayMobController;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\ShopOwner;
use App\Models\Tempid;
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

    public function choose( Request $request)
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
        ShopOwner::where('id',$user->id)->update(['plan_id' => $plan->id,'payment_status'=>'pending']);
        $paymob = new PayMobController();
        return $paymob->checkingOut($user->id, $plan->cost);

    }


    public function checkout_done($id) {
        $shop_owner = ShopOwner::where('id',$id)->first();
        $shop_owner->update(['payment_status' => 'paid']);//,'expires_at' => Carbon::now()->addMonth(), 'is_active' => 1]);
            $shop_owner->starts_at = Carbon::today()->toDateString();
            $exp_data = Carbon::today()->addMonth();
            $exp_data->toDateString();
            $shop_owner->expires_at = $exp_data;
            $shop_owner->is_active = 1;
        Tempid::first()->delete();

        return $this->returnSuccess('payment succeeded', 200);
    }
}
