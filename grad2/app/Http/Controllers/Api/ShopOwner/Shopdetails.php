<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopOwner;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Http\Resources\ShowShopDetails;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Shopdetails extends Controller
{
    use ResponseTrait;

    public function adddetails(Request $request){

        $shop_owner_id = auth('shop_owner')->user();

        $validator = Validator::make($request->all(), [
            'phone_number' => 'unique:shops|min:11',
            'email' =>  'email|unique:shops,email',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }

            Shop::where('shop_owner_id',$shop_owner_id->id)->update([
                'email'=>$request->shop_email,
                'slogan' => $request->slogan,
                'description' => $request->description,
                'instagram' => $request->instagram,
                'facebook' => $request->facebook,
                'phone_number' => $request->shop_phone_number,
            ]);
            return $this->returnSuccess("Details saved",200);
    }

    public function updatedetails(Request $request){

        $shop_owner_id = auth('shop_owner')->user();
        $shopid=auth('shop_owner')->user()->shop()->
        value('id');
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                Rule::unique('shops','email')->ignore($shopid)
            ],
            'shop_phone_number' => [
                'required',
                Rule::unique('shops','phone_number')->ignore($shopid),
            ],
            'name' => [
                     'required',
                     Rule::unique('shops','name')->ignore($shopid),
                 ]
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }

        Shop::where('shop_owner_id',$shop_owner_id->id)->update([
            'name'=>$request->name,
            'slogan' => $request->slogan,
            'description' => $request->description,
            'instagram' => $request->instagram,
            'facebook' => $request->facebook,
            'address'=>$request->address,
            'email'=>$request->email,
            'phone_number'=>$request->shop_phone_number
        ]);
        ShopOwner::where('id',$shop_owner_id->id)->update([

            'site_address' => $request->address,
            'site_name'=> $request->name,
        ]);

        return $this->returnSuccess("Details saved",200);
    }

    public function showdetails(Request $request){

        $shop_id = Shop::where('name',$request->header('shop'))->value('id');
        $shop_owner_id = ShopOwner::whereHas('shop', function ($query) use ($shop_id) {
            $query->where('id',$shop_id);
        })->first()->id;

       $showdetails = Shop::where('shop_owner_id',$shop_owner_id)->first();

        return $this->returnData("About us",new ShowShopDetails($showdetails),200);
    }

    public function dbshowdetails(){

        $shop_owner_id = auth('shop_owner')->user();
        $showdetails = Shop::where('shop_owner_id',$shop_owner_id->id)->first();
        return $this->returnData("About us",new ShowShopDetails($showdetails),200);
    }
}
