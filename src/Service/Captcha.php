<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;


final class HCaptcha
{
    private const HCAPTCHA_ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';

    private HttpClientInterface $httpClient;

    private RequestStack $requestStack;

    private string $HCaptchaSecretkey;


    public function __construct(
        HttpClientInterface $httpClient,
        RequestStack $requestStack,
        string $HCaptchaSecretkey)
    {
        $this->httpClient = $httpClient;
        $this->requestStack = $requestStack;
        $this->HCaptchaSecretkey = $HCaptchaSecretkey;
    }

    public  function isHCaptchaValid(): bool
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return false;
        }

        $options = [
            'headers' => [
                'Accept'  => 'application/json',
                //'Content' => 'application/x-www-form-urlencoded'
            ],
            'body' => [
                'secret' => $this->HCaptchaSecretkey,
                'response' =>  $request->request->get('g-recaptcha-response')
            ]
        ];

        $response = $this->httpClient->request('POST', self::HCAPTCHA_ENDPOINT, $options);

        $data = $response->toArray();

        dd($data);

    }

}