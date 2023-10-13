<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Checkout\Form;
use function route;

class StripeService implements PaymentServiceInterface
{
    use Form;

    /**
     * @param float $paymentTotal
     * @return void
     */
    public function processPayment(float $paymentTotal): void
    {
        $this->getForm($paymentTotal, route('stripe_pay'));
    }
}
