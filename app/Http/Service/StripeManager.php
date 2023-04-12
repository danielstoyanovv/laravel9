<?php

namespace App\Http\Service;

use App\Models\Cart;
use Stripe\StripeClient;

class StripeManager
{
    /**
     * @param int $cartId
     * @return string
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createOrder(int $cartId): string
    {
        $stripe = new StripeClient('sk_test_51MbKefG3ggaQ2SPfczpyzWwktZaWBuCxrDG7VFiA6wsPplY7pl3ed0FgtUveC3PGLzfDRVWCzoreLXHi82s9nbya00lbQGXKMd');

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => [
                $this->getLineItemsData($cartId)
            ],
            'mode' => 'payment',
            'success_url' => route('stripe_success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe_error')
        ]);

        return $checkout_session->url;
    }

    /**
     * @param int $cartId
     * @return array
     */
    public function getLineItemsData(int $cartId): array
    {
        $data = [];

        if ($cart = Cart::find($cartId)) {
            foreach ($cart->getCartItem as $item) {
                $data[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $item->product->getAttributes()['name']
                        ],
                        'unit_amount' => $item->product->getAttributes()['price'] * 100,
                    ],
                    'quantity' => $item->getAttributes()['qty']
                ];
            }
        }

        return $data;
    }

    /**
     * @param string $paymentNumber
     * @param float $amount
     * @return mixed|\Stripe\Refund|void
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function refund(string $paymentNumber, float $amount)
    {
        $stripe = new StripeClient('sk_test_51MbKefG3ggaQ2SPfczpyzWwktZaWBuCxrDG7VFiA6wsPplY7pl3ed0FgtUveC3PGLzfDRVWCzoreLXHi82s9nbya00lbQGXKMd');

        return $stripe->refunds->create(['payment_intent' => $paymentNumber, 'amount' => $amount * 100]);
    }
}
