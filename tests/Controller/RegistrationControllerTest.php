<?php

namespace App\Tests\Controller;

use App\Tests\Service\TestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * @dataProvider urlProvider
     */
    public function testUrls($url):void
    {
        $client = $this->createClientAndFollowRedirects();

        $client->request('GET', $url);

        $client->submitForm("S'inscrire",[
        'registration[firstname]' => 'Jean',
            'registration[name]'  => 'Martin',
            'registration[email]' => 'test@email.com',
            'registration[adreeess]' => 'champ d\'adresse non-visible',
            'registration[villle]' => 'champ de ville non-visible',
            'registration[username]' => 'usernameDeTest',
            'registration[phone]' => '0601020304',
            'registration[password][first]' => 'BadPassword1234',
            'registration[password][second]' => 'BadPassword1234'
            ]);
          $this->assertResponseStatusCodeSame(403);
          $this->assertRouteSame('app_registration');
    }

    /**
     * @dataProvider urlProvider
     */
    public function testIsValid($url){
        $client = $this->createClientAndFollowRedirects();

        $client->request('GET', $url);

        $client->submitForm("S'inscrire",[
            'registration[firstname]' => 'Jean',
            'registration[name]'  => 'Martin',
            'registration[email]' => 'test1@email.com',
            'registration[username]' => 'usernameDeTest45',
            'registration[phone]' => '0601020304',
            'registration[password][first]' => 'BadPassword1234!',
            'registration[password][second]' => 'BadPassword1234!'
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_login');

    }


    public function urlProvider()
    {
        yield ['/Inscription'];

    }

}
