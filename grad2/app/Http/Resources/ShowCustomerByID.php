<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowCustomerByID extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[

                    'first_name' => $this->first_name,
                    'second_name' => $this->second_name,
                    'email' => $this->email,
                    'phone_number' => $this->phone_number,
                    'address' => $this->address,
                    'shop_id' => $this->shop_id,

        ];
    }
}


//namespace App\Http\Resources;
//
//use Illuminate\Http\Resources\Json\ResourceCollection;
//
//class ShowCustomerByID extends ResourceCollection
//{
//    /**
//     * Transform the resource collection into an array.
//     *
//     * @param \Illuminate\Http\Request $request
//     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
//     */
//    public function toArray($request)
//    {
//        return [
//            'data' => $this->collection->transform(function ($data) {
//                return [
//
//                    'first_name' => $data->first_name,
//                    'second_name' => $data->second_name,
//                    'email' => $data->email,
//                    'phone_number' => $data->phone_number,
//                    'address' => $data->address,
//                    'shop_id' => $data->shop_id,
//
//
//                ];
//            }),
//        ];
//    }
//}
