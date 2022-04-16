<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    use ResponseTrait;
    public function showProduct(Request $request)
    {
        $shop_id = auth('api')->user()->shop()->value('id');
        $category=Category::where('shop_id',$shop_id)->where('id',$request->id)->value('id');
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


    public function showprouctid($id)
    {
        $shop_id = auth('api')->user()->shop()->value('id');
        $product  = Product::whereHas('category', function ($query) use($shop_id) {
            $query->where('shop_id',$shop_id);
        })->find($id);
        if (!$product) {
            return $this->returnError(' product not found', 404, true);
        }
        return $this->returnData('chosen product info', $product, 200);
    }
}
