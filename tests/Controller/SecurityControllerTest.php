<?php

namespace App\Tests\Controller;

use App\Tests\Service\TestTrait;


use Symfony\Component\Panther\PantherTestCase;


class SecurityControllerTest extends PantherTestCase
{
    use TestTrait;

    public function createFirefoxClient(){
        return static::createPantherClient(
            [
                'browser' => static::FIREFOX,

                'hostname' => '127.0.0.1',
                'port' => 8080,
            ],
            [],
            [
                'capabilities' => [
                    'acceptInsecureCerts' => true,
                ],
            ]
        );

    }

    public function testlogintrue(){

        //créer un utilisateur via la command app:create-user avec l'email : security@test.com et le mot de passe Password!test1234
        $crawler = $this->createFirefoxClient()->request('GET', '/Connexion');

        $form = $crawler->selectButton('Se connecter')->form(['email' => 'security@test.com', 'password' => 'Password!test1234']);

        $this->createFirefoxClient()->submit($form);
        $this->createFirefoxClient()->takeScreenshot('./var/tests/screenshots/test2.png');

        $this->assertSelectorExists('input[id="search_company_search"]');
    }


    public function testWithoutjavascript(){
        $client = $this->createClientAndFollowRedirects();
        $crawler  = $client->request('GET', '/Connexion');

        $form = $crawler->selectButton('Se connecter')->form(['email' => 'emailtest@gmail.com', 'password' => 'badpassword123!']);
        $client->submit($form);
        $this->assertSelectorTextContains('div[class="alert alert-danger"]','recapcha invalide.');

    }

    /**
     * @dataProvider providerInvalidCredentials
     */
    public function ConnectWidthProvider(
        int $attemptNumber,
        array $invalidCredentials,
        string $flashError,
        string $screenshotsPath)
    {

        if($attemptNumber === 1){
            $this->truncateTableBeforeTest('authentication_log');
        }


        $crawler = $this->createFirefoxClient()->request('GET', '/Connexion');


        $this->assertSelectorTextContains('h1', 'Connexion');


        $form = $crawler->selectButton('Se connecter')->form($invalidCredentials);

        $this->createFirefoxClient()->submit($form);

        $this->createFirefoxClient()->takeScreenshot($screenshotsPath);


        $this->assertSelectorTextContains('div[class="alert alert-danger"]',$flashError);


    }




    public function providerInvalidCredentials(): \Generator
    {

        yield [
            1,
            [
                'email' => 'badEmail@exemple.com',
                'password' => 'fake-password-01'
            ],
            'l\'email ou le mot de passe saisi est incorrecte.',
            './var/tests/screenshots/invalid-credentials-01.png'
        ];

        yield [
            2,
            [
                'email' => 'badEmail@exemple.com',
                'password' => 'fake-password-02'
            ],
            'l\'email ou le mot de passe saisi est incorrecte.',
            './var/tests/screenshots/invalid-credentials-02.png'
        ];
        yield [
            3,
            [
                'email' => 'badEmail@exemple.com',
                'password' => 'fake-password-03'
            ],
            'l\'email ou le mot de passe saisi est incorrecte.',
            './var/tests/screenshots/invalid-credentials-03.png'
        ];
        yield [
            4,
            [
                'email' => 'badEmail@exemple.com',
                'password' => 'fake-password-04'
            ],
            'l\'email ou le mot de passe saisi est incorrecte.',
            './var/tests/screenshots/invalid-credentials-04.png'
        ];
        yield [
            5,
            [
                'email' => 'badEmail@exemple.com',
                'password' => 'fake-password-05'
            ],
            'l\'email ou le mot de passe saisi est incorrecte.',
            './var/tests/screenshots/invalid-credentials-05.png'
        ];
        yield [
            6,
            [
                'email' => 'badEmail@exemple.com',
                'password' => 'fake-password-06'
            ],
            'Trop de tentatives de connexion, vous ne pouvez pas vous reconnecter avant' ,
            './var/tests/screenshots/invalid-credentials-06.png'
        ];

    }



}