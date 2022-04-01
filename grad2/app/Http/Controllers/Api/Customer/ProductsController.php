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
    public function showProduct()
    {
        $shop_id = auth('api')->user()->shop()->first();
        $cat_id = Category::where('shop_id',$shop_id->id)->first();
        $product = Product::where('category_id',$cat_id->id)->get();
        if($product)
        {
            return $this->returnData('ok',$product,200);
        }
        return $this->returnError('Product not found',404);
    }

}
