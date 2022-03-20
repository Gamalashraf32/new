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
    public function updateProduct(Request $request,$id)
    {
        $shop_id = auth('shop_owner')->user()->shop()->first();

        $product = User::where([['shop_id', $shop_id->id]])->first()->find($id);

        $product=Product::find($id);
        if(!$product)
        {
            return $this->returnError('Product not found',404);
        }

        $product->update($request->all());

        if($product)
        {
            return $this->returnSuccess('Product Saved',200);
        }
       return $this->returnError('Product not saved',400);
    }
#==========================================================================================================================
    public function deleteProduct($id)
    {
        $product=Product::find($id);
        if(!$product)
        {
            return $this->returnError('Product not found',404);
        }
        $product->delete($id);
        return $this->returnSuccess('Product Deleted',200);
    }
#==========================================================================================================================
    public function showProduct()
    {
        $product = Product::all();
        if($product)
        {
            return $this->returnData('ok',$product,200);
        }
        return $this->returnError('Product not found',404);
    }
#==========================================================================================================================
#==========================================================================================================================
    public function addoption(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails())
        {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        if (!auth('shop_owner')->user())
        {
            return $this->returnError('you are not authorized to edit this data', 401, false);
        }
        Option::create([
            'product_id'=>$request->product_id,
            'name' => $request->name
        ]);
        return $this->returnSuccess('option saved successfully', 200);
    }
#==========================================================================================================================
    public function updateoption(Request $request,$id)
    {
        $option = Option::find($id);
        if(!$option)
        {
            return $this->returnError('Option not found',404);
        }
        $option->update($request->except('product_id'));
        $option->save();
        if($option)
        {
            return $this->returnSuccess('Option Saved',200);
        }
        return $this->returnError('Option not saved',400);
    }
#==========================================================================================================================
    public function deleteoption($id)
    {
        $option = Option::find($id);
        if(!$option)
        {
            return $this->returnError('Option not found',404);
        }
        $option->delete($id);
        return $this->returnSuccess('Option Deleted',200);
}
#==========================================================================================================================
    public function showoption()
    {
        $option = Option::all();
        if($option){
            return $this->returnData('ok',$option,200);
        }
        return $this->returnError('No option stored',404);
    }
#==========================================================================================================================
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
#==========================================================================================================================
    public function updatevariant(Request $request,$id)
    {
        $variant = ProductVariant::find($id);
        if (!$variant) {
            return $this->returnError('Variant can not found', 404);
        }

//        $variant->value = $request->value;
//        $variant->quantity = $request->quantity;
//        $variant->save();

        $variant->update($request->except(['option_id','product_id']));

        if($variant)
        {
            return $this->returnSuccess('Variant saved',200);
        }
        return $this->returnError('Variant not saved',400);
    }

#==========================================================================================================================
    public function deletevariant($id)
    {
        $variant = ProductVariant::find($id);
        if (!$variant)
        {
            return $this->returnError('Variant not found',404);
        }
        $variant->delete($id);
        return $this->returnSuccess('Variant deleted',200);
    }
#==========================================================================================================================
    public function showvariant()
        {
            $variant = ProductVariant::all();
            if($variant)
            {
                return $this->returnData('ok ',$variant,200 );
            }
            return $this->returnError('No variant stored',404);
        }
}
