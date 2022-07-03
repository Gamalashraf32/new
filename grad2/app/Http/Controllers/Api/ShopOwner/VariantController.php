<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Option;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VariantController extends Controller
{
    use ResponseTrait;

  public function addvariant(Request $request)
     {
        $validator = Validator::make($request->all(), [
            'product_id'=>'required',
            'quantity'=>'required'
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

        $variant=ProductVariant::create([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity
        ]);
        if($request->option_id){
            $variant->option_id = $request->option_id;
        }
        if($request->value && !is_null($request->option_id)){
            $variant->value = $request->value;
        }
        elseif ($request->value && is_null($request->option_id)){
            return $this->returnError('You can not add value for variant without choosing and option', 401, false);
        }
        $variant->save();
        return $this->returnSuccess('variant saved successfully', 200);
      }
#==========================================================================================================================
    public function updatevariant(Request $request,$id)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $variant = ProductVariant::find($id);
        if($variant)
        {
            $shop_id_product=Product::where('id',$variant->product_id)->value('shop_id');
        }
        if (!$variant||($shop_id!=$shop_id_product)) {

            return $this->returnError('Variant can not found', 404);
        }

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
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $variant = ProductVariant::find($id);
        if($variant)
        {
            $shop_id_product=Product::where('id',$variant->product_id)->value('shop_id');
        }
        if (!$variant||($shop_id!=$shop_id_product)) {
            return $this->returnError('Variant not found',404);
        }
        $variant->delete();
        return $this->returnSuccess('Variant deleted',200);
    }
#==========================================================================================================================
    public function showvariantwithid($id)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $variant = ProductVariant::whereHas('cate', function ($query) use ($shop_id) {
            $query->where('shop_id',$shop_id);
        })->find($id);

        if($variant)
        {
            return $this->returnData('ok ',$variant,200 );
        }
        return $this->returnError('No variant stored',404);
    }
#==========================================================================================================================
    public function showvariantproduct($id)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $variant = ProductVariant::where('product_id',$id)->get();

        if($variant)
        {
            return $this->returnData('ok ',$variant,200 );
        }
        return $this->returnError('No variant stored',404);
    }
}
