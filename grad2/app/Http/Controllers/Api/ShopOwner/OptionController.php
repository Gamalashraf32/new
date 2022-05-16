<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Option;
use App\Models\Product;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OptionController extends Controller
{
    use ResponseTrait;

    public function addoption(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails())
        {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        if (!auth('shop_owner')->user())
        {
            return $this->returnError('you are not authorized to edit this data', 401, false);
        }
        Option::create([
            'product_id'=>$request->product_id,
            'name' => $request->name
        ]);
        return $this->returnSuccess('option saved successfully', 200);
    }
#==========================================================================================================================
    public function updateoption(Request $request,$id)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $option = Option::find($id);
        if($option)
        {
            $shop_id_product=Product::where('id',$option->product_id)->value('shop_id');
        }
        if(!$option||($shop_id!=$shop_id_product))
        {
            return $this->returnError('Option not found',404);
        }
        $option->update($request->except('product_id'));
        $option->save();
        if($option)
        {
            return $this->returnSuccess('Option Saved',200);
        }
        return $this->returnError('Option not saved',400);
    }
#==========================================================================================================================
    public function deleteoption($id)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $option = Option::find($id);
        if($option)
        {
            $shop_id_product=Product::where('id',$option->product_id)->value('shop_id');
        }
        if(!$option||($shop_id!=$shop_id_product))
        {
            return $this->returnError('Option not found',404);
        }
        $option->delete();
        return $this->returnSuccess('Option Deleted',200);
    }
#==========================================================================================================================
    public function showoption()
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $option = Option::whereHas('cat', function ($query) use ($shop_id) {
            $query->where('shop_id',$shop_id);
        })->get();

        if($option){
            return $this->returnData('ok',$option,200);
        }
        return $this->returnError('No option stored',404);
    }
#==========================================================================================================================
    public function showoptionwithid($id)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $option = Option::whereHas('cat', function ($query) use ($shop_id) {
            $query->where('shop_id',$shop_id);
        })->find($id);

        if($option){
            return $this->returnData('ok',$option,200);
        }
        return $this->returnError('No option stored',404);
    }
}
