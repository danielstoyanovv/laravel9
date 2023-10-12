<?php

namespace App\Services\Payment;

use App\Checkout\Form;
use function route;

class StripeService implements PaymentServiceInterface
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
