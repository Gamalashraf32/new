<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class gamal extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function ($data) {
                return [

                    'first_name' => $data->first_name,
                    'second_name' => $data->second_name,
                    'email' => $data->email,
                    'phone_number' => $data->phone_number,
                    'address' => $data->address,
                    'shop_id' => $data->shop_id,


                ];
            }),
        ];
    }
}
