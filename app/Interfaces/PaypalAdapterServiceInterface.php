<?php

namespace App\Interfaces;

interface PaypalAdapterServiceInterface
{
    /**
     * @param string $orderId
     * @param string $token
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function capture(string $orderId, string $token): array;

    /**
     * @param string $paypalApiUrl
     * @param string $paypaAuthorizationCode
     * @return mixed|string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getToken();

    /**
     * @param string $orderId
     * @return string
     */
    public function getCheckoutNowUrl(string $orderId): string;

    /**
     * @param float $amount
     * @param string $token
     * @return array|mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function createOrder(float $amount, string $token);
}
