<?php

namespace App\Http\Service\Payment;

interface PaymentInterface
{
    /**
     * @param int $paymentTotal
     * @return void
     */
    public function processPayment(int $paymentTotal): void;
}
