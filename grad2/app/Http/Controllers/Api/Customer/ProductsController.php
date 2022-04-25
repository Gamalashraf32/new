<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOwner;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    use ResponseTrait;
    public function showallProducts(Request $request)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $product  = Product::whereHas('category', function ($query) use($shop_id) {
            $query->where('shop_id',$shop_id);
        })->get();
        if (!$product) {
            return $this->returnError(' product not found', 404, true);
        }
        return $this->returnData('chosen product info', $product, 200);
    }

    public function showCatProducts(Request $request)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $category=Category::where('shop_id',$shop_id)->where('id',$request->category_id)->value('id');
        if(!$category)
        {
            return $this->returnError('not found',404);
        }
        $Product=Product::where('category_id',$category)->get();
        if($Product)
        {
            return $this->returnData('ok',$Product,200);
        }
        return $this->returnError('Product not found',404);
    }


    public function showprouctid(Request $request,$id)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $product  = Product::whereHas('category', function ($query) use($shop_id) {
            $query->where('shop_id',$shop_id);
        })->find($id);
        if (!$product) {
            return $this->returnError(' product not found', 404, true);
        }
        return $this->returnData('chosen product info', $product, 200);
    }

    public function searchproduct(Request $request,$name)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
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
    public function get()
    {
        $shop_id=auth('api')->user()->shop()->value('id');
        $shop_owner=ShopOwner::whereHas('shop',function ($query) use($shop_id)
        {
            $query->where('id',$shop_id) ;
        })->first();
        if($shop_owner->is_active==1)
        {
            //dosomething
        }
        else
        {
            //do some thing
        }
    }
}
