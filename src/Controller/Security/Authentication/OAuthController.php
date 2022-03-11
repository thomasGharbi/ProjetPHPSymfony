<?php

namespace App\Controller\Security\Authentication;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    private const GOOGLE_AUTHORIZATION_ENDPOINT = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const GITHUB_AUTHORIZATION_ENDPOINT = 'https://github.com/login/oauth/authorize';

    /**
     * @Route("/OAuth/Google", name="app_oauth_Google")
     */
    public function connectToGoogleAccount(
        CsrfTokenManagerInterface $csrfTokenManager,
        UrlGeneratorInterface     $urlGenerator
    ): RedirectResponse
    {
        $redirectURL = $urlGenerator->generate('app_login', [
            'oauth-provider' => 'google'
        ], UrlGeneratorInterface::ABSOLUTE_URL);


        $queryParams = http_build_query([
            'client_id' => $this->getParameter('app.oauth_google_client_id'),
            'state' => $csrfTokenManager->getToken('oauth-google-token-csrf')->getValue(),
            'redirect_uri' => $redirectURL,
            'access_type' => 'online',
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'prompt' => 'consent',
        ]);


        return new RedirectResponse(self::GOOGLE_AUTHORIZATION_ENDPOINT . '?' . $queryParams);
    }


    /**
     * @Route("/OAuth/GitHub", name="app_oauth_Github")
     */
    public function connectToGithubAccount(
        CsrfTokenManagerInterface $csrfTokenManager,
        UrlGeneratorInterface     $urlGenerator
    ): RedirectResponse
    {
        $redirectURL = $urlGenerator->generate('app_login', [
            'oauth-provider' => 'github'
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $queryParams = http_build_query([
            'client_id' => $this->getParameter('app.oauth_github_client_id'),
            'state' => $csrfTokenManager->getToken('oauth-github-token-csrf')->getValue(),
            'redirect_uri' => $redirectURL,
            'scope' => 'user:email',
        ]);
        return new RedirectResponse(self::GITHUB_AUTHORIZATION_ENDPOINT . '?' . $queryParams);
    }
}