<?php

namespace App\Security\OAuth2;

use App\Entity\User;
use App\Event\OAuthEvent;
use App\Repository\UserRepository;
use App\Service\SendEmail;
use App\Utils\CreateRandomPassword;
use DateTimeImmutable;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @method UserInterface loadUserByIdentifier(string $identifier)
 */
class OAuthGoogleUserProvider implements UserProviderInterface
{
    private const GOOGLE_TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';

    private const GOOGLE_USERINFO_ENDPOINT = 'https://openidconnect.googleapis.com/v1/userinfo';

    private string $oauthGoogleClientID;

    private string $oauthGoogleClientSecret;

    public UserRepository $userRepository;

    public UrlGeneratorInterface $urlGenerator;

    public HttpClientInterface $httpClient;

    public EventDispatcherInterface $eventDispatcher;

    public TokenGeneratorInterface $tokenGenerator;

    public sendEmail $sendEmail;

    public CreateRandomPassword $createRandomPassword;

    public function __construct(
        string                   $oauthGoogleClientID,
        string                   $oauthGoogleClientSecret,
        UserRepository           $userRepository,
        UrlGeneratorInterface    $urlGenerator,
        HttpClientInterface      $httpClient,
        EventDispatcherInterface $eventDispatcher,
        TokenGeneratorInterface  $tokenGenerator,
        sendEmail                $sendEmail,
        CreateRandomPassword     $createRandomPassword
    )
    {
        $this->oauthGoogleClientID = $oauthGoogleClientID;
        $this->oauthGoogleClientSecret = $oauthGoogleClientSecret;
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->httpClient = $httpClient;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenGenerator = $tokenGenerator;
        $this->sendEmail = $sendEmail;
        $this->createRandomPassword = $createRandomPassword;
    }

    /**
     * @param string $code
     * @return User
     *
     */
    public function loadUserByOAuthGoogle(string $code): User
    {
        $accessToken = $this->getAccessToken($code);

        $googleUserData = $this->getUserGoogleData($accessToken);

        $user = $this->userRepository->getUserFromOAuth('google', $googleUserData['oauth_id'], $googleUserData['email']);

        $googleUserData += ['random_password' => null];

        if (!$user) {

            $randomPassword = $this->tokenGenerator->generateToken();
            $googleUserData['random_password'] = $randomPassword;
            //Envoie un email qui renseignera a l'utilisateur un mot de passe généré
            $this->sendEmailForRandomPassword($googleUserData);

            if (!$googleUserData['verified']) {

                $mustBeVerifiedBefore = new DateTimeImmutable('+ 3days');
                $validationToken = $this->tokenGenerator->generateToken();

                $googleUserData += [
                    'must_be_verified_before' => $mustBeVerifiedBefore,
                    'validation_Token' => $validationToken
                ];
                //Envoie un email de confirmation si le compte OAuth n'est pas verifié
                $this->sendEmailForVerification($googleUserData);
            }

            $user = $this->userRepository->createUserFromOAuth('google', $googleUserData);


        }
        //génére les OauthLogs (monolog)
        $this->eventDispatcher->dispatch(new OAuthEvent($googleUserData), OAuthEvent::USER_CREATE_FROM_OAUTH);

        return $user;


    }

    /**
     * @param string $code
     * @return string
     * Requete "access Token"
     */
    private function getAccessToken(string $code): string
    {

        $redirectURL = $this->urlGenerator->generate('app_login', [
            'oauth-provider' => 'google'
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => [
                'code' => $code,
                'client_id' => $this->oauthGoogleClientID,
                'client_secret' => $this->oauthGoogleClientSecret,
                'redirect_uri' => $redirectURL,
                'grant_type' => 'authorization_code'
            ]
        ];

        $response = $this->httpClient->request('POST', self::GOOGLE_TOKEN_ENDPOINT, $options);

        $data = $response->toArray();

        if (!$data['access_token']) {
            throw new ServiceUnavailableHttpException(null, "L'authentification a echoué");
        }

        return $data['access_token'];
    }


    /**
     * @param string $accessToken
     * @return array<mixed>
     * recuperation des donné utilisateur via OAuth Google
     */
    private function getUserGoogleData(string $accessToken): array
    {
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$accessToken}"
            ]
        ];

        $response = $this->httpClient->request('GET', self::GOOGLE_USERINFO_ENDPOINT, $options);

        $data = $response->toArray();

        if (!$data['sub'] || !$data['email'] || !$data['email_verified'] || !$data['given_name'] || !$data['family_name']) {
            throw new ServiceUnavailableHttpException(null, "L'authentification a echoué, la reponse API n'est pas valide");
        }
        //formate le tableau de donnée Oauth conformement a tout les fournisseur OAuth pour la gestion du UserRepository
        $data = $this->formatArrayKeyOAuth($data);

        return $data;
    }

    /**
     * @param array<mixed> $data
     * @return array<mixed>
     * uniformise des clés du tableau conformement au UserRepository
     */
    private function formatArrayKeyOAuth(array $data): array
    {

        $email = $data['email'];
        $verified = $data['email_verified'];
        $OAuthID = $data['sub'];
        $firstName = $data['given_name'];
        $name = $data['family_name'];

        $data = [
            'oauth_provider' => 'google',
            'email' => $email,
            'verified' => $verified,
            'oauth_id' => $OAuthID,
            'first_name' => $firstName,
            'name' => $name
        ];

        return $data;
    }

    /**
     * @param array<mixed> $googleUserData
     * @return void
     *
     */
    private function sendEmailForRandomPassword(array $googleUserData): void
    {
        $this->sendEmail->send([
            'recipient' => $googleUserData['email'],
            'subject' => "Inscription Avec Google",
            'html_template' => "email/OAuth2/AccountOAuthRandomPassword.html.twig",
            'context' => [
                'oauthProvider' => 'Google',
                'randomPassword' => $googleUserData['random_password'],


            ]
        ]);
    }

    /**
     * @param array<mixed> $googleUserData
     * @return void
     *
     */
    private function sendEmailForVerification(array $googleUserData): void
    {

        $this->sendEmail->send([
            'recipient' => $googleUserData['email'],
            'subject' => "vérification de votre compte",
            'html_template' => "email/OAuth2/AccountOAuthNotVerified.html.twig",
            'context' => [
                'randomPassword' => $googleUserData['random_password'],
                'userID' => $googleUserData['oauth_id'],
                'registrationToken' => $googleUserData['validation_Token'],
                'tokenDuration' => $googleUserData['must_be_verified_before']?->format('d/m/Y à H:i'),
                'oauth_provider' => 'google'
            ]
        ]);


    }

    public function refreshUser(UserInterface $user): User
    {
        if (!$user instanceof User || !$user->getGoogleID()) {
            throw new UnsupportedUserException();
        }
        $googleID = $user->getGoogleID();

        return $this->loadUserByUsername($googleID);
    }

    public function loadUserByUsername(?string $googleID): User
    {
        $user = $this->userRepository->findOneBy([
            'googleID' => $googleID
        ]);

        if (!$user) {
            throw new UserNotFoundException('Utilisateur inexistant');
        }
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }








}