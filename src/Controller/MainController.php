<?php

namespace App\Controller;

use App\Form\SearchCompanyType;
use App\Repository\CompanyRepository;
use App\Repository\NoticeRepository;
use App\Service\AddVisitor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main', defaults: ['public_access' => true], methods: ['GET','POST'])]
    public function main(
        Request $request,
        CompanyRepository $companyRepository,
        NoticeRepository $noticesRepository,
        AddVisitor $addVisitor
    ): Response
    {


        $addVisitor->addPointToVisitor('visitor_main', 1);

        $companies = null;
        $searchResult = false;
        $searchForm = $this->createForm(SearchCompanyType::class);
        $notices = $noticesRepository->findLastNotices();

        $searchForm = $searchForm->handleRequest($request);
        if($searchForm->isSubmitted() && $searchForm->isValid()) {

            $addVisitor->addPointToVisitor('visitor_main_search', 3);
            $companies = $companyRepository->search($searchForm->get('search')->getData(), $searchForm->get('params_search')->getData());


            if ($companies == null) {

                $searchResult = true;
            }

        }

        return $this->render('/main.html.twig', [
            'controller_name' => 'MainController', 'search_form' => $searchForm->createView(), 'companies' => $companies, 'notices' => $notices, 'search_result' => $searchResult
        ]);
    }
}
