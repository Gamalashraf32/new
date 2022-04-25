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
    public function addoption(Request $request)
    {
        $shop_id =auth('shop_owner')->user()->shop()->first()->id;
        $validator = Validator::make($request->all(), [
            'name' => [Rule::unique('options', 'name')->where('shop_id' , $shop_id)]
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
        $shop_id = auth('shop_owner')->user()->shop()->first();
        $cat_id = Category::where('shop_id', $shop_id->id)->first();
        $product_id = Product::where('category_id', $cat_id->id)->first();
        $option = Option::where('product_id', $product_id->id)->first()->find($id);
        $validate = Validator::make($request->all(), [
            'name' => [Rule::unique('options', 'name')->where('shop_id' , $shop_id->id)->ignore($id)]
        ]);
        if ($validate->fails()) {
            $errors = [];
            foreach ($validate->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        if(!$option)
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
        $option = Option::whereHas('cat', function ($query) use ($shop_id) {
            $query->where('shop_id',$shop_id);
        })->find($id);

        if(!$option)
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
