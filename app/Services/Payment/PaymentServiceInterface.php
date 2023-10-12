<?php

namespace App\Services\Payment;

interface PaymentServiceInterface
{
    /**
     * @param int $paymentTotal
     * @return void
     */
    public function processPayment(int $paymentTotal): void;
}
