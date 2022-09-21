<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\PlaceToPayService;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @throws Exception
     */
    public function purchase(Request $request)
    {
        //make a purchase
        $request->validate([
            'name' => 'required|string|max:80',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|numeric|digits:10',
        ]);

        $user = User::query()->firstOrCreate([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', //password
        ]);

        $order = $user->orders()
            ->create([
                'customer_name' => $request->input('name'),
                'customer_email' => $request->input('email'),
                'customer_mobile' => $request->input('phone'),
                'total' => $request->input('amount'),
            ]);

        foreach (json_decode($request->input('cart'), true) as $item) {
            $order->products()
                ->attach($item['id'], ['quantity' => $item['quantity']]);
        }

        try {
            $this->payment($order);

            $order->load('products');
            return $order;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @throws Exception
     */
    public function payment(Order $order)
    {
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
    }
}
