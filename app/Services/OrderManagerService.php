<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\OrderManagerServiceInterface;
use App\Models\Cart;
use App\Models\Order;
use Database\Factories\OrderFactory;
use Database\Factories\OrderItemFactory;

class OrderManagerService implements OrderManagerServiceInterface
{
    public $status;
    public $paymentMethod;
    public $paymentData;
    public $invoiceNumber;

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $paymentMethod
     * @return $this
     */
    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @param string $paymentData
     * @return $this
     */
    public function setPaymentData(string $paymentData): self
    {
        $this->paymentData = $paymentData;
        return $this;
    }

    /**
     * @param string $invoiceNumber
     * @return $this
     */
    public function setInvoiceNumber(string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    /**
     * @param Cart $cart
     * @return void
     */
    public function create(Cart $cart)
    {
        $order = OrderFactory::new([
            'total' => $cart->getTotal(),
            'status' => $this->status,
            'payment_method' => $this->paymentMethod,
            'payment_data' => $this->paymentData ?? '',
            'invoice_number' => $this->invoiceNumber ?? ''
        ])->create();

        $this->handleOrderItems($cart, $order);
    }

    /**
     * @param Cart $cart
     * @param Order $order
     * @return void
     */
    public function handleOrderItems(Cart $cart, Order $order): void
    {
        foreach ($cart->getCartItem as $item) {
            OrderItemFactory::new([
                'qty' => $item->qty,
                'price' => $item->price,
                'product_id' => $item->product->id,
                'order_id' => $order->id
            ])->create();
        }
    }
}
