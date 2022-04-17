<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class SearchController extends Controller
{
    use ResponseTrait;
    public function productsearch($name)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $product  = Product::where('name', 'LIKE', '%'. $name. '%')->whereHas('category', function ($query) use($shop_id) {
            $query->where('shop_id',$shop_id);
        })->get();
        if(count($product)){
            return $this->returnData('founded data',$product,200);
        }
        else
        {
            return $this->returnError('no data found',404);
        }
    }
    public function searchcustomer($name)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $customer=User::where('shop_id',$shop_id)->where('first_name', 'LIKE', '%'. $name. '%')->orWhere('second_name', 'LIKE', '%'. $name. '%')->get();
        if(count($customer)){
            return $this->returnData('founded data',$customer,200);
        }
        else
        {
            return $this->returnError('no data found',404);
        }
    }
    public function searchshipping($name)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $shipping=Shipping::where('shop_id',$shop_id)->where('government', 'LIKE', '%'. $name. '%')->get();
        if(count($shipping)){
            return $this->returnData('founded data',$shipping,200);
        }
        else
        {
            return $this->returnError('no data found',404);
        }
    }


}
