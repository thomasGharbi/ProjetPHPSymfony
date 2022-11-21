<?php

namespace App\Tests\Service;


use App\Entity\Company;
use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;


trait TestTrait
{

    private function createClientAndFollowRedirects(): KernelBrowser
    {
        $client = static::createClient();
        $client->followRedirects();



        return $client;

    }

    private function truncateTableBeforeTest(string $table):void
    {

        $kernel = self::bootKernel();
        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $connection = $entityManager->getConnection()->executeQuery("TRUNCATE TABLE `{$table}`");

        $entityManager->getConnection()->close();

    }

    public function getUuidForUser(){

        $container = $this->client->getContainer();
        $userRepository = $container->get(UserRepository::class);
        $user = $userRepository->findOneWidthoutParams();


        if(!($user instanceof User)){
            //créer un utilisateur via la command app:create-user
            throw new \LogicException('Il n\'y a pas d\'utilisateur en base de donnée de test. Essayez la commande (app:create-user) pour en créer un');
        }

        return $user->getUuid();

    }

    public function getUuidForCompany()
    {
        //dd($this->client->getContainer()->get(CompanyRepository::class));
        $container = $this->client->getContainer();

        $companyRepository = $container->get(CompanyRepository::class);

        $company = $companyRepository->findOneWidthoutParams();

        if(!($company instanceof Company)){

            //créer une entreprise via la command app:create-user
            throw new \LogicException('Il n\'y a pas d\'entreprise en base de donnée de test. Essayez la commande (app:create-user) pour en créer une');
        }

        return $company->getUuid();
    }

    public function authenticationUser(){

        //reutiliser faire une fonction
        $container = $this->client->getContainer();
        $userRepository = $container->get(UserRepository::class);
        $user = $userRepository->findOneWidthoutParams();
        if(!($user instanceof User)){
            //créer un utilisateur via la command app:create-user
            throw new \LogicException('Il n\'y a pas d\'utilisateur en base de donnée de test. Essayez la commande (app:create-user) pour en créer un');
        }

        $this->client->loginUser($user);
    }



    /**
     * @return array<mixed>
     */
    private function getURIs(bool $publicAccess):array
    {

        $router = $this->client->getContainer()->get('router');

        $routerWidthAllParameter = $router->getRouteCollection()->all();

        $publicURI = [];

        foreach ($routerWidthAllParameter as $routerWidthAllParameters) {
            $routeParameter = $routerWidthAllParameters->getDefault('public_access');
            if ($routeParameter !== null && $routeParameter == $publicAccess) {

                $path = $routerWidthAllParameters->getPath();

                if ($routerWidthAllParameters->getDefault('parameter') === 'user_uuid') {

                    $publicURI[] = str_replace('{uuidUser}', $this->getUuidForUser(), $path);
                } elseif ($routerWidthAllParameters->getDefault('parameter') === 'company_uuid') {
                    $publicURI[] = str_replace('{uuidCompany}', $this->getUuidForCompany(), $path);

                }else {
                $publicURI[] = $path;
            }

        }
    }


        return $publicURI;
    }

}
