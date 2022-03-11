<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class OAuthEvent extends Event
{
    public const USER_CREATE_FROM_OAUTH = 'oauth_event';

    private string $oauthProvider;

    private string $oauthID;

    private bool $oauthAccountIsVerified;

    private string $oauthEmail;

    private ?string $randomPassword;


    /**
     * @param array<mixed> $oauthUserData
     */
    public function __construct(
        array $oauthUserData)
    {
        $this->oauthProvider = $oauthUserData['oauth_provider'];
        $this->oauthID = $oauthUserData['oauth_id'];
        $this->oauthAccountIsVerified = $oauthUserData['verified'];
        $this->oauthEmail = $oauthUserData['email'];
        $this->randomPassword = $oauthUserData['random_password'];


    }

    public function getOauthProvider(): string
    {
        return $this->oauthProvider;
    }

    public function getOauthID(): string
    {
        return $this->oauthID;
    }

    public function getOauthAccountIsVerified(): bool
    {
        return $this->oauthAccountIsVerified;
    }

    public function getOauthEmail(): string
    {
        return $this->oauthEmail;
    }

    public function getRandomPassword(): ?string
    {
        return $this->randomPassword;
    }




}