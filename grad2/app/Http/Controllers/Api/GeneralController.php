<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    use ResponseTrait;
    public function options(){
        $options=Option::select('id','name')->get();
        return $this->returnData('options',$options,200);
    }
}
