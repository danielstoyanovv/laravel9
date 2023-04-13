<?php

namespace App\Http\Controllers\Shop;

use App\Http\Service\Payment\PaymentInterface;
use App\Http\Controllers\Controller;
use App\Http\Service\Payment\Paypal;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Service\Payment\Stripe;
use App\Http\Service\Payment\Epay;

class CheckoutController extends Controller
{
    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function index(Request $request): RedirectResponse
    {
        try {
            if ($request->getMethod() == 'POST') {
                if (!empty($request->get('payment_method')) && !empty($request->get('payment_total'))) {
                    $paymentMethod = $request->get('payment_method');
                    $paymentTotal = $request->get('payment_total');

                    switch ($paymentMethod) {
                        case "paypal":
                            $paymentClassInstance = new Paypal();
                            break;
                        case "stripe":
                            $paymentClassInstance = new Stripe();
                            break;
                        case "epay":
                            $paymentClassInstance = new Epay();
                            break;
                    }

                    if ($paymentClassInstance instanceof PaymentInterface) {
                        $paymentClassInstance->processPayment($paymentTotal);
                    }
                    throw new \Exception(sprintf(
                        'Class: %s is not a valid payment class',
                        get_class($paymentClassInstance)
                    ));
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }

        return redirect()->route('homepage');
    }
}
