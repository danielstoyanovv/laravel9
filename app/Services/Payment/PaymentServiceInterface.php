<?php

namespace App\Services\Payment;

interface PaymentServiceInterface
{
    /**
     * @param float $paymentTotal
     * @return void
     */
    public function processPayment(float $paymentTotal): void;
}
