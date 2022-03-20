<?php

namespace App\Http\Controllers\Api\ShopOwner;
use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\Shop;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DiscountCodeController extends Controller
{
    use ResponseTrait;
    public function adddiscountcode(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'code'=>'required|unique:discount_codes,code',
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
                $errors[]=$error;
            }
            return $this->returnError(implode(' , ',$errors),400);
        }

        $user=auth('shop_owner')->user();
        $shop_id=Shop::where('shop_owner_id',$user->id)->value('id');
        DiscountCode::create([
            'shop_id'=>$shop_id,
            'code'=>$request->code,
            'type'=>$request->type,
            'value'=>$request->value,
            'minimum_requirements_value'=>$request->minimum_requirements_value,
            'starts_at'=>$request->starts_at,
            'ends_at'=>$request->ends_at,
        ]);
        return $this->returnSuccess("Discount code added successfully",200);
    }

    public function deletediscount($id)
    {
        $shop_id =auth('shop_owner')->user()->shop()->first()->id;
        $shop_id_dis=DiscountCode::where('id',$id)->value('shop_id');
        if($shop_id==$shop_id_dis) {
            $discount = Discountcode::find($id);
            if (!$discount) {
                return $this->returnError("This code not exist", 400);
            }
            $discount->delete($id);
            return $this->returnSuccess("Discount code deleted", 200);
        }
        else
        {
            return $this->returnError("You are not authorized", 401);
        }
    }

    public function showall()
    {
        $shop_id=auth('shop_owner')->user()->shop()->first();
        $discount=DiscountCode::where('shop_id',$shop_id->id)->get();
        if(!$discount)
        {
            return $this->returnError("No Discount codes exists",400);
        }
        return $this->returnData("Your codes",$discount,200);
    }

    public function update($id ,Request $request)
    {
        $shop_id =auth('shop_owner')->user()->shop()->first()->id;
        $shop_id_dis=DiscountCode::where('id',$id)->value('shop_id');
        if($shop_id==$shop_id_dis) {
            $validate = Validator::make($request->all(), [
                'code' => [Rule::unique('discount_codes', 'code')->ignore($id)]
            ]);
            if ($validate->fails()) {
                $errors = [];
                foreach ($validate->errors()->getMessages() as $message) {
                    $error = implode($message);
                    $errors[] = $error;
                }
                return $this->returnError(implode(' , ', $errors), 400);
            }
            $discount = Discountcode::find($id);
            if (!$discount) {
                return $this->returnError("This code not exist", 400);
            }
            $discount->update($request->all());
            return $this->returnSuccess("Discount Code updated", 200);
        }
        else
        {
            return $this->returnError("You are not authorized", 401);
        }

    }

    public function showone($id)
    {
        $shop_id=auth('shop_owner')->user()->shop()->first()->id;
        $shop_id_dis = DiscountCode::where('id', $id)->value('shop_id');
        if ($shop_id == $shop_id_dis) {
            $discount = Discountcode::find($id);
            if (!$discount) {
                return $this->returnError("No Discount codes exists", 400);
            }
            return $this->returnData("Your code", $discount, 200);
        }
        else
        {
            return $this->returnError("You are not authorized", 401);
        }
    }
}
