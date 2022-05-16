<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopOwner;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Http\Resources\ShowShopDetails;

class Shopdetails extends Controller
{
    use ResponseTrait;

    public function adddetails(Request $request){

        $shop_owner_id = auth('shop_owner')->user();

            Shop::where('shop_owner_id',$shop_owner_id->id)->update([
                'slogan' => $request->slogan,
                'description' => $request->description,
                'instagram' => $request->instagram,
                'facebook' => $request->facebook,
            ]);
            return $this->returnSuccess("Details saved",200);
    }

    public function updatedetails(Request $request){

        $shop_owner_id = auth('shop_owner')->user();

        Shop::where('shop_owner_id',$shop_owner_id->id)->update([
            'name'=>$request->name,
            'slogan' => $request->slogan,
            'description' => $request->description,
            'instagram' => $request->instagram,
            'facebook' => $request->facebook,
            'address'=>$request->address,
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
        $shop_owner_email = ShopOwner::whereHas('shop', function ($query) use ($shop_id) {
            $query->where('id',$shop_id);
        })->first()->email;

       $showdetails = Shop::where('shop_owner_id',$shop_owner_id)->first();

        return $this->returnData("About us",[$shop_owner_email,new ShowShopDetails($showdetails)],200);
    }
}
