<?php

namespace App\Http\Controllers\Api\ShopOwner;
use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DiscountCodeController extends Controller
{
    use ResponseTrait;
    public function adddiscountcode(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'code'=>'required',
            'type'=>'required',
            'value'=>'required',
            'minimum_requirements_value'=>'required',
            'starts_at'=>'required',
            'ends_at'=>'required',
        ]);
        if($validate->fails())
        {
            $errors=[];
            foreach($validate->errors()->getMessages() as $message)
            {
                $error=implode($message);
                $errors=$error;
            }
            return $this->returnError(implode(',',$errors),400);
        }
        DiscountCode::create([
            'code'=>$request->code,
            'type'=>$request->type,
            'value'=>$request->value,
            'minimum_requirements_value'=>$request->minimum_requirements_value,
            'starts_at'=>$request->starts_at,
            'ends_at'=>$request->ends_at,
        ]);
        return $this->returnSuccess("Discount code added successfully",200);
    }
}
