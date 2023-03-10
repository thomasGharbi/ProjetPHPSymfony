<?php

namespace App\Security;

use App\Repository\AuthenticationLogRepository;
use App\Service\Captcha;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';


    private UrlGeneratorInterface $urlGenerator;

    private BrutForceChecker $brutForceChecker;

    private Captcha $Captcha;

    
    /**
     * __construct
     *
     * @param  UrlGeneratorInterface $urlGenerator
     * @
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        BrutForceChecker $brutForceChecker,
        Captcha $Captcha)
    {
        $this->urlGenerator = $urlGenerator;
        $this->brutForceChecker = $brutForceChecker;
        $this->Captcha = $Captcha;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $userIp = $request->getClientIp();

        $isBlackListed = $this->brutForceChecker->checkIfBlackListed($userIp);


        //verification de la permission de connexion via brutForceChecker
        if($isBlackListed !== null){
            throw new CustomUserMessageAccountStatusException("Trop de tentatives de connexion, vous ne pouvez pas vous reconnecter avant $isBlackListed");
        }elseif (!$this->Captcha->isHCaptchaValid()){
            throw new CustomUserMessageAccountStatusException("recapcha invalide.");
        }

        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]);
        
    }

   public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
   {
       return parent::onAuthenticationFailure($request, $exception);
   }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {

            return new RedirectResponse($targetPath);
        }

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('app_main'));
        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
        
    }



    protected function getLoginUrl(Request $request): string
    {
        
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);

    }
}
