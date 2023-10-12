<?php

namespace App\Checkout;

use App\Models\Cart;
use App\Services\CalculatesHmac;

trait Form
{
    use CalculatesHmac;

    /**
     * @param float $paymentTotal
     * @param string $actionUrl
     * @return void
     */
    public function getForm(float $paymentTotal, string $actionUrl): void
    {
        $token = csrf_token();
        $formHtml = <<<HTML
<form action= '$actionUrl' method='POST' id='form' style='display:none'>
<input type="hidden" name="_token" value='$token'>
<input type='hidden' name='price' value='$paymentTotal'>
</form>
HTML;
        $form = "<body onload=\"document.getElementById('form').submit();\">";
        $form .= $formHtml;
        echo $form;

        exit;
    }

    /**
     * @param float $paymentTotal
     * @param Cart $cart
     * @return void
     * @throws \Exception
     */
    public function getEpayForm(float $paymentTotal, Cart $cart): void
    {
        $secret = config('epay.secret_code');
        $min = config('epay.client_code');
        $actionUrl = config('epay.action_url');
        $paymentNumber = $cart->getAttributes()['id'];
        $cart->update([
            'invoice_number' => $paymentNumber
        ]);
        $invoice = $paymentNumber;
        $sum = $paymentTotal;
        $expDate = new \DateTime('+' . intval(60) . ' minutes');
        $exp_date = $expDate->format('d.m.Y');
        $descr = $paymentNumber;
        $successActionUrl = route('epay_success');
        $errorActionUrl = route('epay_error');

        $data = <<<DATA
MIN={$min}
INVOICE={$invoice}
AMOUNT={$sum}
EXP_TIME={$exp_date}
DESCR={$descr}
DATA;

        $ENCODED = base64_encode($data);
        $CHECKSUM = $this->hmac('sha1', $ENCODED, $secret);

        $formHtml = <<<HTML
<form action="$actionUrl" method="POST" id="form" style="display:inline">
<input type=hidden name=PAGE value="paylogin">
<input type=hidden name=ENCODED value="$ENCODED">
<input type=hidden name=CHECKSUM value="$CHECKSUM">
<input type=hidden name=URL_OK value="$successActionUrl">
<input type=hidden name=URL_CANCEL value="$errorActionUrl">
</form>
HTML;

        $form = "<body onload=\"document.getElementById('form').submit();\">";
        $form .= $formHtml;
        echo $form;

        exit;
    }
}
