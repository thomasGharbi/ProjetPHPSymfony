<?php

namespace App\Tests;

use App\Tests\Service\TestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SmokeTestProtectedUrls extends WebTestCase
{
    use TestTrait;

    public function setUp(): void
    {
        $this->client = $this->createClientAndFollowRedirects();
        $this->authenticationUser();
    }

    /**
     * @return void
     */
    public function testIfProtectedRoutesAreResponse200():void
    {
        $ProtectedURI = $this->getURIs(false);


        $countOfProtectedURI = count($ProtectedURI);
        $countOfSuccessFullProtectedURI = 0;
        $uriNotLoadedSuccessfully = [];

        foreach ($ProtectedURI as $uri)
        {
            $this->client->request('GET',$uri);
            if($this->client->getResponse()->getStatusCode() === Response::HTTP_OK)
            {
                $countOfSuccessFullProtectedURI += 1;
            }else{
                $uriNotLoadedSuccessfully[] = $uri;
            }
        }

        if(!empty($uriNotLoadedSuccessfully)){
            dump($uriNotLoadedSuccessfully);
        }


        $this->assertSame($countOfProtectedURI, $countOfSuccessFullProtectedURI);
    }
}