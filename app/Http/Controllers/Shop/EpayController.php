<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Checkout\Form;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use App\Interfaces\OrderManagerServiceInterface;

class EpayController extends Controller
{
    use Form;
    private $orderManagerService;
    public function __construct() {
        $this->orderManagerService = App::make(OrderManagerServiceInterface::class);

    }

    public function success(Request $request)
    {
        try {
            DB::beginTransaction();

            session()->flash('message', __('The payment was successful'));
            if ($cart = Cart::find($request->getSession()->get('cart_id'))) {
                $this->orderManagerService
                    ->setPaymentMethod('Epay')
                    ->setInvoiceNumber($cart->invoice_number)
                    ->create($cart);
                $cart->delete();
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
        }

        return redirect()->route('shop');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function pay(Request $request): RedirectResponse
    {
        try {
            if ($request->getMethod() == 'POST' && !empty($request->get('price'))) {
                if ($cart = Cart::find($request->getSession()->get('cart_id'))) {
                    $this->getEpayForm((float) $request->get('price'), $cart);
                }
            }
        } catch (\Exception $exception) {
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
     * @return void
     */
    public function notification(Request $request)
    {
        $STATUS_PAID = 'PAID';
        $STATUS_DENIED = 'DENIED';
        $STATUS_EXPIRED = 'EXPIRED';

        $ENCODED = $request->request->get('encoded');
        $CHECKSUM = $request->request->get('checksum');


        if ($ENCODED && $CHECKSUM) {
            $hmac = $this->hmac('sha1', $ENCODED, "2F6STHESSOXFW1T4VTGFG5C56KPU2R84XXAXAZQEJ5JMA2XGWL8CFG4TAWYT8BDK");

            if ($hmac == $CHECKSUM) {
                $data = base64_decode($ENCODED, true);
                $lines = explode("\n", $data);
                $info = '';

                foreach ($lines as $line) {
                    if (preg_match("/^INVOICE=(\d+):STATUS=(PAID|DENIED|EXPIRED)(:PAY_TIME=(\d+):STAN=(\d+):BCODE=([0-9a-zA-Z]+))?$/", $line, $regs)) {
                        $invoice = $regs[1];
                        $status = $regs[2];
                        $pay_date = $regs[4];
                        $stan = $regs[5];
                        $bcode = $regs[6];

                        $order = Order::where('invoice_number', $invoice)->first();

                        if ($order) {
                            switch ($status) {
                                case $STATUS_PAID:
                                    $info .= "INVOICE=$invoice:STATUS=OK\n";
                                    //$order->setStatus($STATUS_PAID);
                                    $order->update([
                                        'status' => $STATUS_PAID,
                                    ]);

                                    break;
                                case $STATUS_DENIED:
                                    $info .= "INVOICE=$invoice:STATUS=OK\n";
                                    break;
                                case $STATUS_EXPIRED:
                                    $info .= "INVOICE=$invoice:STATUS=OK\n";
                                    break;
                                default:
                                    $info .= "INVOICE=$invoice:STATUS=ERR\n";
                            }
                        } else {
                            $info .= "INVOICE=$invoice:STATUS=NO\n";
                        }
                    }
                }

                echo $info, "\n";
                exit;
            } else {
                echo "ERR=Not valid CHECKSUM\n";
                exit;
            }
        }

        echo "ERR=Missing POST parameters\n";
        exit;
    }
}
