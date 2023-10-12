<?php

namespace App\Interfaces;

use App\Models\Cart;
use App\Models\Order;

interface OrderManagerServiceInterface
{
    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): \App\Services\OrderManagerService;

    /**
     * @param string $paymentMethod
     * @return $this
     */
    public function setPaymentMethod(string $paymentMethod): \App\Services\OrderManagerService;

    /**
     * @param string $paymentData
     * @return $this
     */
    public function setPaymentData(string $paymentData): \App\Services\OrderManagerService;

    /**
     * @param string $invoiceNumber
     * @return $this
     */
    public function setInvoiceNumber(string $invoiceNumber): \App\Services\OrderManagerService;

    /**
     * @param Cart $cart
     * @return void
     */
    public function create(Cart $cart);

    /**
     * @param Cart $cart
     * @param Order $order
     * @return void
     */
    public function handleOrderItems(Cart $cart, Order $order): void;
}
