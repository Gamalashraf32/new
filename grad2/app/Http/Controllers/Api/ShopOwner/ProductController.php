<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Productimage;
use App\Traits\ImageUpload;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ImageUpload,ResponseTrait;
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
        $user_id=auth('shop_owner')->user();
        $product=Product::whereHas('shop', function ($query) use($user_id) {
            $query->where('shop_owner_id',$user_id->id) ;
        })->find($id);

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
        $shop_id= auth('shop_owner')->user()->shop()->first();
        $cat_id = Category::where('shop_id',$shop_id->id)->first();
        $product = Product::where('category_id', $cat_id->id)->first()->find($id);
        // dd($product);
        if(!$product)
        {
            return $this->returnError('Product not found',404);
        }
        $product->delete();
        return $this->returnSuccess('Product Deleted',200);
    }
#==========================================================================================================================
    public function showProduct()
    {
        $shop_id = auth('shop_owner')->user()->shop()->first();
        $cat_id = Category::where('shop_id',$shop_id->id)->first();
        $product = Product::where('category_id',$cat_id->id)->get();
        if($product)
        {
            return $this->returnData('ok',$product,200);
        }
        return $this->returnError('Product not found',404);
    }
}
