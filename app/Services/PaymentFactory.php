<?php

namespace App\Services;

use App\Contracts\PaymentInterface;
use Exception;

class PaymentFactory
{
    public function initializePayment(string $type): PaymentBase
    {
        if ($type == 'PlaceToPay') {
            return new PlaceToPayPayment();
        } elseif ($type == 'PayPal') {
            return new PayPalPayment();
        }

        throw new Exception('Medio de pago no soportado');
    }
}
