<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Service\OrderManager;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use App\Http\Service\StripeAdapter;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function __construct(private StripeAdapter $stripeAdapter, private OrderManager $orderManager)
    {
    }

    public function success(Request $request)
    {
        try {
            if (!empty($_GET['session_id'])) {
                $stripe = new StripeClient(config('stripe.stripe_client_code'));

                if ($session = $stripe->checkout->sessions->retrieve($_GET['session_id'])) {
                    DB::beginTransaction();
                    session()->flash('message', __('The payment was successful'));
                    if ($cart = Cart::find($request->getSession()->get('cart_id'))) {
                        $this->orderManager->create(
                            $cart,
                            strtoupper($session->status) ?? '',
                            'Stripe',
                            $session->payment_intent ?? ''
                        );
                        $cart->delete();
                    }
                    DB::commit();
                }
            }
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
        }

        return redirect()->route('shop');
    }

    /**
     * @return RedirectResponse
     */
    public function error(): RedirectResponse
    {
        session()->flash('message', __('The payment was not successful'));

        return redirect()->route('cart');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function pay(Request $request)
    {
        try {
            if ($request->getMethod() == 'POST' && !empty($request->get('price'))) {
                if ($checkoutLink = $this->stripeAdapter->createOrder($request->getSession()->get('cart_id'))) {
                    return redirect($checkoutLink);
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }

        return redirect()->route('shop');
    }


    public function refund(Request $request)
    {
        try {
            if ($request->getMethod() == 'POST') {
                if (!empty($request->get('paymentNumber') && !empty($request->get('amount')))) {
                    if ($order = Order::where('payment_data', $request->get('paymentNumber'))->first()) {
                        $amount = $request->get('amount');
                        if ($amount > $order->getAttributes()['total']) {
                            session()->flash('message', __("You can't refund more than your order total"));

                            $response = new RedirectResponse($request->headers->get('referer'));
                            $response->setStatusCode(422);
                            return $response;
                        }

                        $refundData = $this->stripeAdapter->refund($request->get('paymentNumber'), $amount);
                        if (!empty($refundData->status) && $refundData->status ===  'succeeded') {
                            session()->flash('message', __("Payment was refunded"));

                            $order = $this->handleRefundData($request, $amount, $order);

                            return redirect($request->headers->get('referer'));
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }

        return redirect()->route('shop');
    }

    /**
     * @param Request $request
     * @param float $amount
     * @param Order $order
     * @return Order
     * @throws \Stripe\Exception\ApiErrorException
     */
    private function handleRefundData(Request $request, float $amount, Order $order): Order
    {
        if ($amount == $order->getAttributes()['total']) {
            $order->update([
                'status' => 'REFUND',
                'refund_amount' => $amount
            ]);
        } elseif ($amount < $order->getAttributes()['total'] && $order->getAttributes()['status'] != 'PARTLY REFUND') {
            $order->update([
                'status' => 'PARTLY REFUND',
                'refund_amount' => $amount

            ]);
        } elseif ($amount + $order->getAttributes()['refund_amount'] < $order->getAttributes()['total'] && $order->getAttributes()['status'] == 'PARTLY REFUND') {
            $order->update([
                'refund_amount' => $amount + $order->getAttributes()['refund_amount']
            ]);
        } elseif ($amount + $order->getAttributes()['refund_amount'] == $order->getAttributes()['total'] && $order->getAttributes()['status'] == 'PARTLY REFUND') {
            $order->update([
                'status' => 'REFUND',
                'refund_amount' => $amount + $order->getAttributes()['refund_amount']
            ]);
        }

        return $order;
    }
}
