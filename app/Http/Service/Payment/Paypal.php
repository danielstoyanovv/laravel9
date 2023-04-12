<?php

namespace App\Http\Service\Payment;

use App\Checkout\Form;

class Paypal implements PaymentInterface
{
    use Form;

    /**
     * @param int $paymentTotal
     * @return void
     */
    public function processPayment(int $paymentTotal): void
    {
        $this->getForm($paymentTotal, route('paypal_pay'));
    }
}
