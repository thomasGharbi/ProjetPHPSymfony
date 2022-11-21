<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Service\TestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountVerification extends WebTestCase
{
    use TestTrait;

    public function setUp(): void
    {
        $this->client = $this->createClientAndFollowRedirects();
        $this->authenticationUser();
    }

    public function testVerificationUserAccount():void
    {
        $container = $this->client->getContainer();
        $userRepository = $container->get(UserRepository::class);
        $user = $userRepository->findOneBy(['isVerified' => false]);

        if(!($user instanceof User)){
            //créer un utilisateur non vérifieé via la command app:create-user
            throw new \LogicException('Il n\'y a pas d\'utilisateur non-vérifié en base de donnée de test. Essayez la commande (app:create-user) pour en créer un');
        }

        $this->client->Request('GET', "/Verification/{$user->getId()}/{$user->getRegistrationToken()}");

        $this->assertSelectorTextContains('h1', 'Connexion');



    }
}