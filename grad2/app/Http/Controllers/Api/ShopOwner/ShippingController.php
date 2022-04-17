<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    use  ResponseTrait;
    public function add(Request $request){
        $validator = Validator::make($request->all(), [
            'government' => 'required',
            'price' => 'required',
            'duration'=>'required'
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        Shipping::create([
           'shop_id'=>$shop_id,
           'government'=>$request->government,
            'price'=>$request->price,
            'duration'=>$request->duration
        ]);
        return $this->returnSuccess('shipping info saved successfully', 200);
    }
    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'government' => 'required',
            'price' => 'required',
            'duration'=>'required'
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $shid = Shipping::where('shop_id', $shop_id)->find($id);
        if (!$shid) {
            return $this->returnError('shipping not found', 404, true);
        }
        $shid->update([
            'government' => $request->government,
            'price' => $request->price,
            'duration'=>$request->duration
        ]);
        return $this->returnSuccess('shipping info updated successfully', 200);
    }
    public function delete($id)
    {

        $shop_id = auth('shop_owner')->user()->shop()->value('id');

        $shipping = Shipping::where('shop_id', $shop_id)->find($id);
        if (!$shipping) {
            return $this->returnError('shipping info not found', 404, true);
        }
        $shipping->delete();
        return $this->returnSuccess('shipping info deleted successfully', 200);
    }
    public function show()
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $ship= Shipping::where('shop_id', $shop_id)->get();
        if (!$ship) {
            return $this->returnError(' no shipping info yet', 404, true);
        }
        return $this->returnData('your shipping info is', $ship->makeHidden(["id","shop_id","updated_at","created_at"]), 200);
    }
    public function showid($id)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $ship= Shipping::where('shop_id', $shop_id)->find($id);
        if (!$ship) {
            return $this->returnError(' no shipping like that', 404, true);
        }
        return $this->returnData('your shipping info is', $ship->makeHidden(["id","shop_id","updated_at","crated_at"]), 200);
    }

}


