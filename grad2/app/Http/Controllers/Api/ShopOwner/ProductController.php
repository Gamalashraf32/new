<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Option;
use App\Models\Product;
use App\Models\Productimage;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Traits\ImageUpload;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;

class ProductController extends Controller
{
    use ResponseTrait;
    use ImageUpload;
    public function addProduct(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
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

        }if(!auth('shop_owner')->user())
         {
             return $this->returnError('you are not authorized to edit this data', 401, false);
         }

        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'brand' => $request->brand,
        ]);
        foreach ($request->file('images') as $image) {
            Productimage::create([
                'product_id' => $product->id,
                'image' => $this->uploadImage($image, 'products-images', 60)
            ]);
        }
        DB::beginTransaction();

      foreach ($request['options'] as $option) {

          Option::create([
              'product_id'=>$product->id,
              'name' => $option
          ]);
      }

        foreach ($request['variant'] as $variant){

            $option_id = Option::where('product_id',$product->id)->where('name',$variant['option'])->first()->id;
                ProductVariant::create([
                'option_id' => $option_id,
                'product_id' => $product->id,
                'quantity' => $variant['quantity'],
                'value' => $variant['value']
           ]);

        }
        DB::commit();

        return $this->returnSuccess('product saved successfully', 200);
    }

#==========================================================================================================================
    public function updateProduct(Request $request,$id)
    {
        $user=auth('shop_owner')->user();
         $product=Product::whereHas('shop', function ($query) use($user) {
               $query->where('shop_owner_id',$user->id) ;
           })->find($id);

        if(!$product)
        {
            return $this->returnError('Product not found',404);
        }

        $options = Option::where('product_id',$product->id);
        foreach ($options as $option){
           $option->update([
                'name' => $request->option
            ]);
            $options->save();
        }

        //dd($request);

        if(!$options)
        {
            return $this->returnError('Option not found',404);
        }

        $variants = ProductVariant::where('product_id',$product->id);

        foreach ($variants as $variant)
        {
            $optionid = Option::where('product_id',$product->id)->where('name',$variant['option'])->first()->id;
            $variant->update([
                'option_id' => $optionid,
                'quantity' => $variant['quantity'],
                'value' => $variant['value']
            ]);
        }

        if (!$variants)
        {
            return $this->returnError('Variant can not found', 404);
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

        $user=auth('shop_owner')->user();
        $product=Product::whereHas('shop', function ($query) use($user) {
            $query->where('shop_owner_id',$user->id) ;
        })->find($id);

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
        $user=auth('shop_owner')->user();
        $product=Product::whereHas('shop', function ($query) use($user) {
            $query->where('shop_owner_id',$user->id);
        })->get();

        if(!isEmpty($product))
        {
            return $this->returnData('ok',$product,200);
        }
        return $this->returnError('Product not found',404);
    }
#==========================================================================================================================
    public function showProductwithid($id)
    {
        $user=auth('shop_owner')->user();
        $product=Product::whereHas('shop', function ($query) use($user) {
            $query->where('shop_owner_id',$user->id);
        })->find($id);

        if($product)
        {
            return $this->returnData('ok',$product,200);
        }
        return $this->returnError('Product not found',404);
    }
}
