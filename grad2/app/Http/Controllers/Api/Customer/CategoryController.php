<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use  ResponseTrait;
    public function show()
    {

        $shop_id = auth('api')->user()->shop()->first();

        $cat = Category::where([['shop_id', $shop_id->id]])->get();
        if (!$cat) {
            return $this->returnError(' no categories yet', 404, true);
        }


        return $this->returnData('categories', $cat->makeHidden(['created_at','updated_at']), 200);
    }
#==========================================================================================================================
    public function showcatid($id)
    {
        $shop_id = auth('api')->user()->shop()->first();

        $cat = Category::where([['shop_id', $shop_id->id]])->first()->find($id);
        if (!$cat) {
            return $this->returnError(' category not found', 404, true);
        }
        return $this->returnData('chosen categories', $cat->makeHidden(['created_at','updated_at']), 200);
    }
}
