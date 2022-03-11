<?php

namespace App\Security\OAuth2;


use App\Entity\User;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OAuthAuthenticator extends AbstractAuthenticator
//OAuth2: Google|Github
{
    private mixed $OAuthProvider;
    private User $user;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private OAuthGoogleUserProvider $OAuthGoogleUserProvider;
    private OAuthGithubUserProvider $OAuthGithubUserProvider;
    private UrlGeneratorInterface $urlGenerator;
    private RouterInterface $router;
    private SessionInterface $session;


    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        OAuthGoogleUserProvider   $OAuthGoogleUserProvider,
        OAuthGithubUserProvider   $OAuthGithubUserProvider,
        UrlGeneratorInterface     $urlGenerator,
        RouterInterface           $router,
        SessionInterface          $session


    )
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->OAuthGoogleUserProvider = $OAuthGoogleUserProvider;
        $this->OAuthGithubUserProvider = $OAuthGithubUserProvider;
        $this->urlGenerator = $urlGenerator;
        $this->router = $router;
        $this->session = $session;


    }

    public function supports(Request $request): ?bool
    {

        $this->OAuthProvider = $request->query->get('oauth-provider');

        //récupéré par le AuthenticationSubscriber
        $this->session->set('oauthProvider', $this->OAuthProvider);

        return $request->query->has('oauth-provider');

    }


    public function authenticate(Request $request): SelfValidatingPassport
    {

        $this->stateChecker($request);
         $code = $request->query->get('code');

        if (!$code || !is_string($code) || !$this->OAuthProvider) {
            throw new AccessDeniedException('Connexion Invalide');
        }
        //OAuth GOOGLE
        if ($this->OAuthProvider === 'google') {
            return new SelfValidatingPassport(new UserBadge($code, function () use ($code) {

                return $this->user = $this->OAuthGoogleUserProvider->loadUserByOAuthGoogle($code);


            }));
        }

        //OAuth GITHUB
        if($this->OAuthProvider === 'github') {
            return new SelfValidatingPassport(new UserBadge($code, function () use ($code) {
                return $this->user = $this->OAuthGithubUserProvider->loadUserByOAuthGithub($code);

            }));
        }



    }

    /**
     * @param Request $request
     * @return void
     * Verifie le "state" générer dans le OAuthController
     */
    private function stateChecker(Request $request): void
    {
        $state = $request->query->get('state');


        if (!$state || is_string($state) && $this->OAuthProvider == 'google' && !$this->csrfTokenManager->isTokenValid(new CsrfToken('oauth-google-token-csrf', $state))
                    || is_string($state) && $this->OAuthProvider == 'github' && !$this->csrfTokenManager->isTokenValid(new CsrfToken('oauth-github-token-csrf', $state)))
        {


            throw new AccessDeniedException('Connexion Invalide');
        }


    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
    {

        return new RedirectResponse($this->router->generate('app_user_dashboard'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->router->generate('app_login'));

    }

    public function createAuthenticatedToken(PassportInterface $passport, string $firewallName): TokenInterface
    {
        return parent::createAuthenticatedToken($passport, $firewallName);
    }


}
