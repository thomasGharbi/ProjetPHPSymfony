<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;


final class Captcha
{
    private const CAPTCHA_ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';

    private HttpClientInterface $httpClient;

    private RequestStack $requestStack;

    private string $CaptchaSecretkey;


    public function __construct(
        HttpClientInterface $httpClient,
        RequestStack $requestStack,
        string $CaptchaSecretkey)
    {
        $this->httpClient = $httpClient;
        $this->requestStack = $requestStack;
        $this->CaptchaSecretkey = $CaptchaSecretkey;
    }

    /**
     * @return array<mixed>|bool
     *
     */
    public  function isHCaptchaValid(): array|bool
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return false;
        }

        $options = [
            'headers' => [
                'Accept'  => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => [
                'secret' => $this->CaptchaSecretkey,
                'response' =>  $request->request->get('g-recaptcha-response')
            ]
        ];

        $response = $this->httpClient->request('POST', self::CAPTCHA_ENDPOINT, $options);

        $data = $response->toArray();

        if(is_array($data) && array_key_exists('success', $data))
        {
            return $data['success'];

        }

        return false;
    }

}