<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\Payment\EpayService;
use App\Services\Payment\PaymentServiceInterface;
use App\Services\Payment\PaypalService;
use App\Services\Payment\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

                    match ($paymentMethod) {
                        "paypal" => $paymentClassInstance = new PaypalService(),
                        "stripe" => $paymentClassInstance = new StripeService(),
                        "epay" => $paymentClassInstance = new EpayService()
                    };

                    if ($paymentClassInstance instanceof PaymentServiceInterface) {
                        $paymentClassInstance->processPayment((float) $paymentTotal);
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
