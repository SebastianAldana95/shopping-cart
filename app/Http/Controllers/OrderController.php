<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PlaceToPayService;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @throws Exception
     */
    public function summary(Order $order)
    {
        $order = Order::query()->findOrFail($order->id);
        $response = PlaceToPayService::getRequestInfo($order);
        $order->status = $response['status']['status'];
        $order->save();

        $order->load('products');

        return $order;
    }

    /**
     * @throws Exception
     */
    public function payment(Order $order)
    {
        //$order = Order::query()->findOrFail(or);
        $response = PlaceToPayService::createRequest($order);

        if ($response['status']['status'] == 'OK') {
            $order->status = 'CREATED';
            $order->transaction_id = $response['requestId'];
            $order->transaction_url = $response['processUrl'];
            $order->save();
        } else {
            $response->status()->message();
        }

        return $order;
        // return redirect()->away($response['processUrl']);
    }
}
