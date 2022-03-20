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

        $shop_id = auth('shop_owner')->user()->shop()->first();

        $cat = Category::where([['shop_id', $shop_id->id]])->get();
        if (!$cat) {
            return $this->returnError(' no categories yet', 404, true);
        }


        return $this->returnData('category deleted successfully', $cat, 200);
    }

}
