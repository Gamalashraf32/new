<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Traits\ResponseTrait;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use ResponseTrait;

    public function addCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        $shop_id = auth('shop_owner')->user()->shop()->first();
        Category::create([
            'shop_id' => $shop_id->id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return $this->returnSuccess('category saved successfully', 200);

    }

    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'brand' => 'required',
            'quantity' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'brand' => $request->brand,
            'quantity' => $request->quantity,
        ]);
        return $this->returnSuccess('product saved successfully', 200);
    }

}
