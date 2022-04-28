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
    public function updatevariant(Request $request,$id)
    {
        $shop_id = auth('shop_owner')->user()->shop()->value('id');
        $variant = ProductVariant::whereHas('cate', function ($query) use ($shop_id) {
            $query->where('shop_id',$shop_id);
        })->find($id);

        if (!$variant) {
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
        $variant = ProductVariant::whereHas('cate', function ($query) use ($shop_id) {
            $query->where('shop_id',$shop_id);
        })->find($id);

        if (!$variant)
        {
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
}
