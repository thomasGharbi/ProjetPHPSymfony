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

class OAuthGithubUserProvider implements userProviderInterface
{
    private const GITHUB_TOKEN_ENDPOINT = 'https://github.com/login/oauth/access_token';

    private const GITHUB_USERINFO_ENDPOINT = 'https://api.github.com/user/emails';
    public UrlGeneratorInterface $urlGenerator;
    public HttpClientInterface $httpClient;
    public UserRepository $userRepository;
    public TokenGeneratorInterface $tokenGenerator;
    public sendEmail $sendEmail;
    public EventDispatcherInterface $eventDispatcher;
    public CreateRandomPassword $createRandomPassword;
    private string $oauthGithubClientID;
    private string $oauthGithubClientSecret;

    public function __construct(
        string                   $oauthGithubClientID,
        string                   $oauthGithubClientSecret,
        UrlGeneratorInterface    $urlGenerator,
        HttpClientInterface      $httpClient,
        UserRepository           $userRepository,
        TokenGeneratorInterface  $tokenGenerator,
        EventDispatcherInterface $eventDispatcher,
        sendEmail                $sendEmail,
        CreateRandomPassword     $createRandomPassword,

    )
    {
        $this->oauthGithubClientID = $oauthGithubClientID;
        $this->oauthGithubClientSecret = $oauthGithubClientSecret;
        $this->urlGenerator = $urlGenerator;
        $this->httpClient = $httpClient;
        $this->userRepository = $userRepository;
        $this->tokenGenerator = $tokenGenerator;
        $this->eventDispatcher = $eventDispatcher;
        $this->sendEmail = $sendEmail;
        $this->createRandomPassword = $createRandomPassword;
    }

    /**
     * @param string $code
     * @return User
     *
     */
    public function loadUserByOAuthGithub(string $code): User
    {
        $accessToken = $this->getAccessToken($code);

        $githubUserData = $this->getUserGithubData($accessToken);

        $user = $this->userRepository->getUserFromOAuth('github', $githubUserData['oauth_id'], $githubUserData['email']);

        $githubUserData += ['random_password' => null];

        if (!$user) {

            $randomPassword = $this->createRandomPassword->createRandomPassword();
            $githubUserData['random_password'] = $randomPassword;

            //Envoie un email qui renseignera a l'utilisateur un mot de passe généré
            $this->sendEmailForRandomPassword($githubUserData);

            if (!$githubUserData['verified']) {

                $mustBeVerifiedBefore = new DateTimeImmutable('+ 3days');
                $validationToken = $this->tokenGenerator->generateToken();

                $githubUserData += [
                    'must_be_verified_before' => $mustBeVerifiedBefore,
                    'validation_Token' => $validationToken
                ];
                //Envoie un email de confirmation si le compte OAuth n'est pas verifié
                $this->sendEmailForVerification($githubUserData);
            }

            $user = $this->userRepository->createUserFromOAuth('github', $githubUserData);


        }
        $OauthEvent = new OAuthEvent($githubUserData);
        //génére les OauthLogs (monolog)
        $this->eventDispatcher->dispatch($OauthEvent,OAuthEvent::USER_CREATE_FROM_OAUTH);

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
            'oauth-provider' => 'github'
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => [
                'code' => $code,
                'client_id' => $this->oauthGithubClientID,
                'client_secret' => $this->oauthGithubClientSecret,
                'redirect_uri' => $redirectURL,

            ]
        ];

        $response = $this->httpClient->request('POST', self::GITHUB_TOKEN_ENDPOINT, $options);

        $data = $response->toArray();

        if (!$data['access_token']) {
            throw new ServiceUnavailableHttpException(null, "L'authentification a echoué");
        }

        return $data['access_token'];
    }

    /**
     * @param string $accessToken
     * @return array<mixed>
     * recuperation des donné utilisateur via OAuth Github
     */
    private function getUserGithubData(string $accessToken): array
    {
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$accessToken}"
            ]
        ];

        $response = $this->httpClient->request('GET', self::GITHUB_USERINFO_ENDPOINT, $options);


        $data = $response->toArray();


        //id github => 12546854+email@+user... (récuperation dé l'identifiant uniquement)
        $githubID = strstr($data[1]['email'], '+', true);

        $data = $data[0] += [
            'github_id' => $githubID
        ];
        if (!$data['email'] || !$data['verified']) {
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
    public function formatArrayKeyOAuth(array $data): array
    {

        $email = $data['email'];
        $verified = $data['verified'];
        $OAuthID = $data['github_id'];

        $data = [
            'oauth_provider' => 'github',
            'email' => $email,
            'verified' => $verified,
            'oauth_id' => $OAuthID
        ];

        return $data;
    }

    /**
     * @param array<mixed> $githubUserData
     * @return void
     *
     */
    private function sendEmailForRandomPassword(array $githubUserData): void
    {

        $this->sendEmail->send([
            'recipient' => $githubUserData['email'],
            'subject' => "Inscription Avec Github",
            'html_template' => "Security/Authentication/email/OAuth2/AccountOAuthRandomPassword.html.twig",
            'context' => [
                'oauthProvider' => 'Github',
                'randomPassword' => $githubUserData['random_password'],
                'userID' => $githubUserData['oauth_id'],
            ]
        ]);
    }

    /**
     * @param array<mixed> $githubUserData
     * @return void
     *
     */
    private function sendEmailForVerification(array $githubUserData): void
    {

        $this->sendEmail->send([
            'recipient' => $githubUserData['email'],
            'subject' => "vérification de votre compte",
            'html_template' => "Security/Authentication/email/OAuth2/AccountOAuthNotVerified.html.twig",
            'context' => [
                'randomPassword' => $githubUserData['random_password'],
                'userID' => $githubUserData['oauth_id'],
                'registrationToken' => $githubUserData['validation_Token'],
                'tokenDuration' => $githubUserData['must_be_verified_before']?->format('d/m/Y à H:i'),
                'oauthProvider' => 'github'
            ]
        ]);


    }

    public function refreshUser(UserInterface $user): User
    {


        if (!$user instanceof User || !$user->getGithubID()) {
            throw new UnsupportedUserException();
        }
        $githubID = $user->getGithubID();
        return $this->loadUserByUsername($githubID);
    }

    public function loadUserByUsername(?string $githubID): User
    {
        $user = $this->userRepository->findOneBy([
            'githubID' => $githubID
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