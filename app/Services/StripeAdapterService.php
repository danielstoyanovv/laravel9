<?php

namespace App\Services;

use App\Models\Cart;
use Stripe\StripeClient;
use function config;
use function route;

class StripeAdapterService
{
    /**
     * @param int $cartId
     * @return string
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createOrder(int $cartId): string
    {
        $stripe = new StripeClient(config('stripe.stripe_client_code'));

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
        $stripe = new StripeClient(config('stripe.stripe_client_code'));

        return $stripe->refunds->create(['payment_intent' => $paymentNumber, 'amount' => $amount * 100]);
    }
}
