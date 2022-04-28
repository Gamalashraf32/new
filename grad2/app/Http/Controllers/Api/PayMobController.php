<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\ShopOwner\PlanController;
use App\Models\CombinedOrder;
use App\Models\ShopOwner;
use App\Models\Tempid;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use BaklySystems\PayMob\Facades\PayMob;

class PayMobController extends Controller
{

    use ResponseTrait;
    /**
     * Display checkout page.
     *
     * @param $shop_owner_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkingOut($shop_owner_id, $price)
    {
        $response = Http::withHeaders([
            'content-type' => 'application/json'
        ])->post('https://accept.paymobsolutions.com/api/auth/tokens',[
            "api_key"=> env('PAYMOB_API_KEY')
        ]);
        $json=$response->json();


        $order = ShopOwner::findOrFail($shop_owner_id);

        $response_final=Http::withHeaders([
            'content-type' => 'application/json'
        ])->post('https://accept.paymobsolutions.com/api/ecommerce/orders',[
            "auth_token"=>$json['token'],
            "delivery_needed"=>"false",
            "amount_cents"=>$price*100,
            "merchant_order_id" =>rand(10,100)

        ]);




            Tempid::create([
                'code' => $order->id
                ]);

        $json_final=$response_final->json();
        $response_final_final=Http::withHeaders([
            'content-type' => 'application/json'
        ])->post('https://accept.paymobsolutions.com/api/acceptance/payment_keys',[
            "auth_token"=>$json['token'],
            "expiration"=> 36000,
            "amount_cents"=>$json_final['amount_cents'],
            "order_id"=>$json_final['id'],
            "billing_data"=>[
                "apartment"=> "NA",
                "email"=> $order->email,
                "floor"=> "NA",
                "first_name"=> $order->first_name,
                "street"=> $order->site_address,
                "building"=> "NA",
                "phone_number"=> $order->phone_number ,
                "shipping_method"=> "NA",
                "postal_code"=> "NA",
                "city"=> $order->city,
                "country"=> $order->country,
                "last_name"=> $order->second_name,
                "state"=> $order->government,
            ],
            "currency"=>"EGP",
            "integration_id"=>env('PAYMOB_INTEGRATION_ID')
        ]);

        $response_final_final_json=$response_final_final->json();
        return $this->returnData('iframe link',
            'https://accept.paymobsolutions.com/api/acceptance/iframes/'.env('PAYMOB_IFRAME_ID') .'?payment_token=' . $response_final_final_json['token'],
            200);
    }


    /**
     * Transaction succeeded.
     *
     */
    protected function succeeded(): \Illuminate\Http\JsonResponse
    {
        // Updating order payment status
        //$checkout = new PlanController();
        //return $checkout->checkout_done();
    }


    /**
     * Transaction failed.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function failed(): \Illuminate\Http\JsonResponse
    {
        $id=Tempid::first()->value('code');
        $checkout = new PlanController();
        return $checkout->checkout_done($id);
    }

    /**
     * Transaction voided.
     *
    =     * @return RedirectResponse
     */
    protected function voided(): \Illuminate\Http\JsonResponse
    {
        return $this->returnError('payment failed', 401);
    }

    /**
     * Transaction refunded.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function refunded(): \Illuminate\Http\JsonResponse
    {
        return $this->returnError('payment failed', 401);
    }

    /**
     * Processed callback from PayMob servers.
     * Save the route for this method in PayMob dashboard >> processed callback route.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */


    public function processedCallback(Request $request)
    {
        // Statuses.
        $isSuccess  = filter_var($request['success'], FILTER_VALIDATE_BOOLEAN);
        $isVoided  = filter_var($request['is_voided'], FILTER_VALIDATE_BOOLEAN);
        $isRefunded  = filter_var($request['is_refunded'], FILTER_VALIDATE_BOOLEAN);

        if ($isSuccess && !$isVoided && !$isRefunded) { // transcation succeeded.
            return $this->succeeded();
        } elseif ($isSuccess && $isVoided) { // transaction voided.
            return $this->voided();
        } elseif ($isSuccess && $isRefunded) { // transaction refunded.
            return $this->refunded();
        } elseif (!$isSuccess)  { // transaction failed.
            return $this->failed();
        }
    }

}
