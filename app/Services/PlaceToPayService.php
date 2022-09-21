<?php

namespace App\Services;

use Exception;
use Http;
use Request;

class PlaceToPayService
{

    private static $secretKey;
    private static $login;
    private static $urlBase;

    private static function initialize()
    {
        self::$secretKey = config('services.place_to_pay.secret_key');
        self::$login = config('services.place_to_pay.login');
        self::$urlBase = config('services.place_to_pay.url_base');
    }

    /**
     * @throws Exception
     */
    public static function getAuth()
    {
        self::initialize();

        $seed = date('c');
        $secretKey = self::$secretKey;

        $nonce = bin2hex(random_bytes(16));
        $nonceBase64 = base64_encode($nonce);
        $tranKey = base64_encode(sha1($nonce . $seed . $secretKey, true));


        return array(
            'login' => self::$login,
            'seed' => $seed,
            'nonce' => $nonceBase64,
            'tranKey' => $tranKey,
        );
    }

    /**
     * @throws Exception
     */
    public static function createRequest($order)
    {
        self::initialize();

        $endpoint = self::$urlBase.'/api/session';
        $returnURL = url('/summary/'.$order->id);

        $credentials = self::getAuth();

        $response = Http::post($endpoint, [
            'auth' => $credentials,
            'payment' => [
                'reference' => $order->id,
                'description' => 'Orden para la referencia'.$order->id,
                'amount' => [
                    'currency' => 'COP',
                    'total' => $order->total,
                ],
            ],
            'expiration' => date('c', strtotime('+ 1 hour')),
            'returnUrl' => $returnURL,
            'ipAddress' => '127.0.0.1',
            'userAgent' => 'PlacetoPay Sandbox',
        ]);

        return $response->json();
    }

    /**
     * @throws Exception
     */
    public static function getRequestInfo($order)
    {
        self::initialize();

        $endpoint = self::$urlBase.'/api/session/'.$order->transaction_id;
        $credentials = self::getAuth();

        $response = Http::post($endpoint, [
            'auth' => $credentials
        ]);

        return $response->json();

    }

}
