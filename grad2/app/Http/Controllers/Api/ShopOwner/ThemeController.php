<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Theme;
use App\Models\Shop;

class ThemeController extends Controller
{
    use  ResponseTrait;

    public function chooseTheme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'font' => 'required',
            'primary_color' => 'required',
            'secondary_color' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                $error = implode($message);
                $errors[] = $error;
            }
            return $this->returnError(implode(' , ', $errors), 400);
        }
        $user=auth('shop_owner')->user();
        $shop_id=Shop::where('shop_owner_id',$user->id)->value('id');
        $theme=Theme::where('shop_id',$shop_id);
        if(is_null($theme)){
        $theme = Theme::Create([
            'shop_id'=>$shop_id,
            'name'=>$request->name,
            'font'=>$request->font,
            'primary_color'=>$request->primary_color,
            'secondary_color'=>$request->secondary_color,
        ]);
        return $this->returnSuccess('theme saved successfully', 200);
    }
        else{
            return $this->returnError("Theme Already Selected", 400);
        }
    }

    public function update(Request $request)
    {
        $user=auth('shop_owner')->user();
        $shop_id=Shop::where('shop_owner_id',$user->id)->value('id');
        $id=Theme::where('shop_id',$shop_id)->value('id');
        $theme=Theme::find($id);
        $theme->update($request->all());
        return $this->returnSuccess("Theme updated",200);
    }
    public function show_theme_info()
    {
        $shop_id=auth('shop_owner')->user()->shop()->value('id');
        $theme=Theme::where('shop_id',$shop_id)->first();
        return $this->returnData('theme info',$theme,200);
    }
    public function show_theme(Request $request)
    {
        $shop_id = Shop::where('name', $request->header('shop'))->value('id');
        $theme=Theme::where('shop_id',$shop_id)->first();
        return $this->returnData('theme info',$theme,200);
    }

}
