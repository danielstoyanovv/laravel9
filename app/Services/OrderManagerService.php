<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use Database\Factories\OrderFactory;
use Database\Factories\OrderItemFactory;

class OrderManagerService
{
    public function create(Cart $cart, string $status, string $paymentMethod, string $paymentData = '', string $invoiceNumber = '')
    {
        $order = OrderFactory::new([
            'total' => $cart->getTotal(),
            'status' => $status,
            'payment_method' => $paymentMethod,
            'payment_data' => $paymentData,
            'invoice_number' => $invoiceNumber
        ])->create();

        $this->handleOrderItems($cart, $order);
    }

    /**
     * @param Cart $cart
     * @param Order $order
     * @return void
     */
    private function handleOrderItems(Cart $cart, Order $order): void
    {
        foreach ($cart->getCartItem as $item) {
            OrderItemFactory::new([
                'qty' => $item->getAttributes()['qty'],
                'price' => $item->getAttributes()['price'],
                'product_id' => $item->product->getAttributes()['id'],
                'order_id' => $order->getAttributes()['id']

            ])->create();
        }
    }
}
