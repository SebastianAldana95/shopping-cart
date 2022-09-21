<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PlaceToPayService;
use Exception;

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
}
