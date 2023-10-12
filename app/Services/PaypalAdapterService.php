<?php

namespace App\Services;

use Symfony\Component\HttpClient\CurlHttpClient;
use function config;
use function route;

class PaypalAdapterService
{
    public function __construct(private CurlHttpClient $client)
    {
    }

    /**
     * @param string $orderId
     * @param string $token
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function capture(string $orderId, string $token): array
    {
        $result = [];
        $captureResponseJson = $this->client->request(
            "POST",
            config('paypal.paypal_api_url') . "/v2/checkout/orders/" . $orderId .  "/capture",
            [
                "headers" => [
                    "Content-Type" => "application/json",
                    "Authorization" => "Bearer " . $token
                ],
            ]
        );

        if (!empty($captureResponseJson)) {
            $result = json_decode($captureResponseJson->getContent(), true);
        }

        return $result;
    }

    /**
     * @param string $paypalApiUrl
     * @param string $paypaAuthorizationCode
     * @return mixed|string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getToken()
    {
        $token = '';

        $tokenJsonResponse = $this->client->request(
            'POST',
            config('paypal.paypal_api_url') . '/v1/oauth2/token?grant_type=client_credentials',
            [
                'headers' => [
                    'Authorization' => 'Basic ' . config('paypal.paypal_authorization_code')
                ]
            ]
        );

        $tokenResponse = json_decode($tokenJsonResponse->getContent(), true);

        if (!empty($tokenResponse['access_token'])) {
            $token = $tokenResponse['access_token'];
        }

        return $token;
    }

    /**
     * @param string $orderId
     * @return string
     */
    public function getCheckoutNowUrl(string $orderId): string
    {
        return  config('paypal.paypal_url') . "/checkoutnow?token=" . $orderId;
    }

    /**
     * @param float $amount
     * @param string $token
     * @return array|mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function createOrder(float $amount, string $token)
    {
        $result = [];
        $payPalRequestId = rand();
        $referenceId = rand();
        $orderResponseJson = $this->client->request(
            "POST",
            config('paypal.paypal_api_url') . "/v2/checkout/orders",
            [
                "headers" => [
                    "Content-Type" => "application/json",
                    "Authorization" => "Bearer " . $token,
                    "PayPal-Request-Id" => $payPalRequestId,
                ],
                "json" =>
                    [
                        "intent" => "CAPTURE",
                        "purchase_units" => [[
                            "reference_id" => $referenceId,
                            "amount" => [
                                "value" => $amount,
                                "currency_code" => "USD"
                            ]
                        ]],
                        "payment_source" => [
                            "paypal" => [
                                "experience_context" => [
                                    "payment_method_preference" => "IMMEDIATE_PAYMENT_REQUIRED",
                                    "payment_method_selected" => "PAYPAL",
                                    "brand_name" => "EXAMPLE INC",
                                    "locale" => "en-US",
                                    "landing_page" => "LOGIN",
                                    "user_action" => "PAY_NOW",
                                    "cancel_url" => route('paypal_error'),
                                    "return_url" => route('paypal_success')
                                ]
                            ]
                        ]
                    ]
            ]
        );

        if (!empty($orderResponseJson->getContent())) {
            $result = json_decode($orderResponseJson->getContent(), true);
        }

        return $result;
    }
}
