<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('create', function () {
    $result = Http::asForm()
        ->withBasicAuth(config('services.paypal.clientId'), config('services.paypal.secretKey'))
        ->post(config('services.paypal.urlResource') . 'oauth2/token', [
            'grant_type' => 'client_credentials'
        ]);

    if ($result->ok()) {
        $accessToken = $result->json()['access_token'];
        echo $accessToken.PHP_EOL;

        $order = Http::
        withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post(config('services.paypal.urlResource') . 'checkout/orders', [
            'purchase_units' => [
                [
                    "reference_id" => uniqid(),
                    'amount' => [
                        'currency' => 'USD',
                        'total' => '1.0'
                    ],
                ]
            ],
            'redirect_urls' => [
                'return_url' => 'https://google.com',
                'cancel_url' => 'https://google.com'
            ]
        ]);

        if ($order->created()) {
            $id = $order->json()['id'];
            echo "Id => $id"; // Con este approved
            $links = collect($order->json()['links'])->where('method','=','REDIRECT')->first();
            $link = $links['href'];
            dd($link);
        }

    } else {
        throw new Exception('Error de credenciales');
    }
});

#https://www.sandbox.paypal.com/

Artisan::command('pay {reference}', function ($reference) {
   $response = Http::withHeaders(['Authorization' => 'Bearer A21AAI4jR2Vt_no6daQATUbEq-xozp7by5NW2XnPU3DokM05GH8G_rcHphtXaxXqVUNU3mshW2g_0MeZyQT8pjughx5oDItVA'])
    ->post(config('services.paypal.urlResource')."checkout/orders/{$reference}/pay", [
       'disbursement_mode' => 'INSTANT'
   ]);

   dd($response->body());
});

