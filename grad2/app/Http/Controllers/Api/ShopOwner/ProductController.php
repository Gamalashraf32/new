<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Option;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Productimage;
use App\Models\ProductVariant;
use App\Traits\ImageUpload;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function PHPUnit\Framework\isEmpty;

class ProductController extends Controller
{
    use ResponseTrait;
    use ImageUpload;
    public function addProduct(Request $request)
    {
        $user=auth('shop_owner')->user();
        $shop_id=Shop::where('shop_owner_id',$user->id)->value('id');
        $validator = Validator::make($request->all(), [
            'name' => [Rule::unique('products', 'name')->where('shop_id' , $shop_id)],
            'description' => 'required',
            'price' => 'required',
            'brand' => 'required',
            'images' => 'required',
            'images.*' => 'file|mimes:png,jpg,jpeg|max:4096',

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
            'shop_id' => $shop_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'brand' => $request->brand,
        ]);
        $imgnum=0;
        foreach ($request->file('images') as $image) {
            $imgnum++;
            Productimage::create([
                'product_id' => $product->id,
                'image' => $this->uploadImage($image, 'products-images', 60)
            ]);
            if($imgnum==1){
                $product->ProductImage = $this->uploadImage($image, 'products-images', 60);
                $product->save();
            }
        }
        return $this->returnData('product saved successfully',$product->id, 200);
    }

#==========================================================================================================================
    public function updateProduct(Request $request,$id)
    {
        $shop_id=auth('shop_owner')->user()->shop()->first()->id;
        $product=Product::where('shop_id',$shop_id)->find($id);

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

        $shop_id=auth('shop_owner')->user()->shop()->first()->id;
        $product=Product::where('shop_id',$shop_id)->find($id);

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
        $shop_id=auth('shop_owner')->user()->shop()->first()->id;
        $product=Product::where('shop_id',$shop_id)->get();
        if(!is_null($product))
        {
            return $this->returnData('ok',$product,200);
        }
        return $this->returnError('Product not found',404);
    }
#==========================================================================================================================
    public function showProductwithid($id)
    {
        $shop_id=auth('shop_owner')->user()->shop()->first()->id;
        $product=Product::where('shop_id',$shop_id)->where('id',$id)->first();

        if($product)
        {
            $options=Option::where('product_id',$product->id)->get();
            $vatiants=ProductVariant::where('product_id',$product->id)->get();
            $images=Productimage::where('product_id',$product->id)->get();
            $data=[
                'product'=>$product,
                'options'=>$options,
                'variants'=>$vatiants,
                'image'=>$images
            ];
            return $this->returnData('ok',$data,200);
        }
        return $this->returnError('Product not found',400);
    }
#==========================================================================================================================
    public function validator(Request $request)
    {
        $shop_id=auth('shop_owner')->user()->shop()->first()->id;
        $product=Product::where('shop_id',$shop_id)->where('name',$request->name)->first();
        if($product)
        {
            $options=Option::where('product_id',$product->id)->get();
            $vatiants=ProductVariant::where('product_id',$product->id)->get();
            $images=Productimage::where('product_id',$product->id)->get();
            $data=[
                'product'=>$product,
                'options'=>$options,
                'variants'=>$vatiants,
                'image'=>$images
            ];
            return $this->returnData('product found',$data,200);
        }
        else
        {
            return $this->returnError('Product not found',400);
        }
    }
}
