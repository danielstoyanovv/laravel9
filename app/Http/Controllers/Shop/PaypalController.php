<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Interfaces\OrderManagerServiceInterface;
use App\Interfaces\PaypalAdapterServiceInterface;
use App\Models\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaypalController extends Controller
{
    private $orderManagerService;
    private $paypal;
    public function __construct() {
        $this->orderManagerService = App::make(OrderManagerServiceInterface::class);
        $this->paypal = App::make(PaypalAdapterServiceInterface::class);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|void
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function success(Request $request)
    {
        try {
            DB::beginTransaction();

            if ($request->get('token') && $request->get('PayerID')) {
                session()->flash('message', __('The payment was successful'));
                $captureResponse = $this->paypal->capture($request->get('token'), $this->getToken($request));
                if ($cart = Cart::find($request->getSession()->get('cart_id'))) {
                    $this->orderManagerService
                        ->setStatus($captureResponse['status'])
                        ->setPaymentMethod('Paypal')
                        ->create($cart);
                    $cart->delete();
                }
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
        }

        return redirect()->route('shop');
    }


    public function error(Request $request)
    {
        session()->flash('message', __('The payment was not successful'));

        return redirect()->route('cart');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function pay(Request $request)
    {
        try {
            if ($request->getMethod() == 'POST' && !empty($request->get('price'))) {
                if ($token = $this->getToken($request)) {
                    $orderResponse = $this->paypal->createOrder((float) $request->get('price'), $token);
                    if (!empty($orderResponse['id'])) {
                        return redirect($this->paypal->getCheckoutNowUrl($orderResponse['id']));
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
        return redirect()->route('homepage');
    }

    /**
     * @param Request $request
     * @return string
     */
    private function getToken(Request $request): string
    {
        if ($request->getSession()->get('token')) {
            return $request->getSession()->get('token');
        }

        $token = $this->paypal->getToken();
        $request->getSession()->set('token', $token);

        return  $token;
    }
}
