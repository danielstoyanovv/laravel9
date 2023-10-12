<?php

namespace App\Services\Payment;

use App\Checkout\Form;
use function route;

class EpayService implements PaymentServiceInterface
{
    use Form;

    /**
     * @param int $paymentTotal
     * @return void
     */
    public function processPayment(int $paymentTotal): void
    {
        $this->getForm($paymentTotal, route('epay_pay'));
    }
}
