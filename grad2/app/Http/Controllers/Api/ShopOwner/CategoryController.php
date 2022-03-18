<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Option;
use App\Models\Product;
use App\Models\Productimage;
use App\Models\ProductVariant;
use App\Models\User;
use App\Traits\ImageUpload;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use ResponseTrait, ImageUpload;

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
        if (!auth('shop_owner')->user()) {
            return $this->returnError('you are not authorized to edit this data', 401, false);
        }
        $shop_id = auth('shop_owner')->user()->shop()->first();

        Category::create([
            'shop_id' => $shop_id->id,
            'name' => $request->name,
            'description' => $request->description,
        ]);


        return $this->returnSuccess('category saved successfully', 200);

    }

#==========================================================================================================================
    public function updateCategory(Request $request, $id)
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
        if (!auth('shop_owner')->user()) {
            return $this->returnError('you are not authorized to edit this data', 401, false);
        }
        $shop_id = auth('shop_owner')->user()->shop()->first();


        $cat = Category::where([['shop_id', $shop_id->id]])->first()->find($id);
        if (!$cat) {
            return $this->returnError('category not found', 404, true);
        }
        $cat->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);


        return $this->returnSuccess('category updated successfully', 200);

    }

#==========================================================================================================================
    public function deletecat($id)
    {
        if (!auth('shop_owner')->user()) {
            return $this->returnError('you are not authorized to edit this data', 401, false);
        }
        $shop_id = auth('shop_owner')->user()->shop()->first();

        $cat = Category::where([['shop_id', $shop_id->id]])->first()->find($id);
        if (!$cat) {
            return $this->returnError('category not found', 404, true);
        }
        $cat->delete();


        return $this->returnSuccess('category deleted successfully', 200);
    }

#==========================================================================================================================
    public function showcat()
    {
        if (!auth('shop_owner')->user()) {
            return $this->returnError('you are not authorized to show this data', 401, false);
        }
        $shop_id = auth('shop_owner')->user()->shop()->first();

        $cat = Category::where([['shop_id', $shop_id->id]])->get();
        if (!$cat) {
            return $this->returnError(' no categories yet', 404, true);
        }


        return $this->returnData('category deleted successfully', $cat, 200);
    }

#==========================================================================================================================
    public function addProduct(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'brand' => 'required',
            'images' => 'required',
            'images.*' => 'file|mimes:png,jpg,jpeg|max:4096'
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        if (!auth('shop_owner')->user()) {
            return $this->returnError('you are not authorized to edit this data', 401, false);
        }
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'brand' => $request->brand,
        ]);
        //$image = $request->file('images');
        foreach ($request->file('images') as $image) {
            Productimage::create([
                'product_id' => $product->id,
                'image' => $this->uploadImage($image, 'products-images', 60)
            ]);
        }
        return $this->returnSuccess('product saved successfully', 200);
    }

#==========================================================================================================================
    public function addoption(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        if (!auth('shop_owner')->user()) {
            return $this->returnError('you are not authorized to edit this data', 401, false);
        }
        Option::create([
            'product_id'=>$request->product_id,
            'name' => $request->name
        ]);
        return $this->returnSuccess('option saved successfully', 200);
    }

#==========================================================================================================================

    public function addvariant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'option_id'=>'required',
            'product_id'=>'required',
            'value'=>'required'
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        if(!auth('shop_owner')->user())
        {
            return $this->returnError('you are not authorized to edit this data', 401, false);
        }

                ProductVariant::create([
                    'option_id' => $request->option_id,
                    'product_id' => $request->product_id,
                    'value' => $request->value,
                    'quantity' => $request->quantity
                ]);
                return $this->returnSuccess('variant saved successfully', 200);
    }


}
