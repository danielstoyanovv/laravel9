<?php

namespace App\Http\Service\Payment;

use App\Checkout\Form;

class Stripe implements PaymentInterface
{
    use Form;

    /**
     * @param int $paymentTotal
     * @return void
     */
    public function processPayment(int $paymentTotal): void
    {
        $this->getForm($paymentTotal, route('stripe_pay'));
    }
}
