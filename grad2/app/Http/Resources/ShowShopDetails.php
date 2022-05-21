<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowShopDetails extends JsonResource
{

    public function toArray($request)
    {

        return [

            'name' => $this->name,
            'slogan'=>$this->slogan,
            'facebook'=>$this->facebook,
            'instagram'=>$this->instagram,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'description' =>$this->description,
            'email'=>$this->email,
        ];

    }
}
