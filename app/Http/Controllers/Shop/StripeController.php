<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Interfaces\OrderManagerServiceInterface;
use App\Interfaces\StripeAdapterServiceInterface;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Illuminate\Support\Facades\App;

class StripeController extends Controller
{
    private $orderManagerService;
    private $stripeAdapter;
    public function __construct() {
        $this->orderManagerService = App::make(OrderManagerServiceInterface::class);
        $this->stripeAdapter = App::make(StripeAdapterServiceInterface::class);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function success(Request $request): RedirectResponse
    {
        try {
            if (!empty($_GET['session_id'])) {
                $stripe = new StripeClient(config('stripe.stripe_client_code'));

                if ($session = $stripe->checkout->sessions->retrieve($_GET['session_id'])) {
                    DB::beginTransaction();
                    session()->flash('message', __('The payment was successful'));
                    if ($cart = Cart::find($request->getSession()->get('cart_id'))) {
                        $this->orderManagerService
                            ->setStatus(strtoupper($session->status))
                            ->setPaymentMethod('Stripe')
                            ->setPaymentData($session->payment_intent)
                            ->create($cart);
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

                        $refundData = $this->stripeAdapter->refund($request->get('paymentNumber'), (float) $amount);
                        if (!empty($refundData->status) && $refundData->status ===  'succeeded') {
                            session()->flash('message', __("Payment was refunded"));

                            $order = $this->handleRefundData($request, (float) $amount, $order);

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
