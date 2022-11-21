<?php

namespace App\Tests;


use App\Tests\Service\TestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;



 class SmokeTestPublicUrls extends WebTestCase
{
     use TestTrait;


     public function setUp(): void
     {
         $this->client = $this->createClientAndFollowRedirects();
     }


     /**
      * @return void
      */
     public function testIfPublicRoutesAreResponse200():void
     {

         $publicURI = $this->getURIs(true);



         $countOfPublicURI = count($publicURI);
         $countOfSuccessFullPublicURI = 0;
         $uriNotLoadedSuccessfully = [];

         foreach ($publicURI as $uri)
         {
             $this->client->request('GET',$uri);
             if($this->client->getResponse()->getStatusCode() === Response::HTTP_OK)
             {
                 $countOfSuccessFullPublicURI += 1;
             }else{
                 $uriNotLoadedSuccessfully[] = $uri;
             }
         }

         if(!empty($uriNotLoadedSuccessfully)){
             dump($uriNotLoadedSuccessfully);
         }


         $this->assertSame($countOfPublicURI, $countOfSuccessFullPublicURI);

     }



 }
