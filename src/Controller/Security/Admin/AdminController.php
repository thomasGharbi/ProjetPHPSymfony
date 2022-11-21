<?php

namespace App\Controller\Security\Admin;

use App\Entity\Company;
use App\Entity\User;
use App\Form\Security\Admin\AdminPasswordType;
use App\Form\Security\Admin\SearchsAdminType;
use App\Repository\AuthenticationLogRepository;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use App\Repository\VisitorRepository;
use App\Security\BrutForceChecker;
use App\Service\DeleteCompany;
use App\Service\DeleteUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\LogicException;


class AdminController extends AbstractController
{

    private CompanyRepository $companyRepository;
    private UserRepository $userRepository;
    private AuthenticationLogRepository $AuthRepository;
    private DeleteCompany $deleteCompany;
    private DeleteUser $deleteUser;
    private SessionInterface $session;
    private BrutForceChecker $brutForceChecker;


    public function __construct(
        CompanyRepository           $companyRepository,
        UserRepository              $userRepository,
        AuthenticationLogRepository $AuthRepository,
        DeleteCompany               $deleteCompany,
        DeleteUser                  $deleteUser,
        SessionInterface            $session,
        BrutForceChecker            $brutForceChecker
    )
    {
        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
        $this->AuthRepository = $AuthRepository;
        $this->deleteCompany = $deleteCompany;
        $this->deleteUser = $deleteUser;
        $this->session = $session;
        $this->brutForceChecker = $brutForceChecker;
    }


    #[Route('/admin-demo', name: 'app_admin', defaults: ['public_access' => false],methods: ['GET','POST'])]
    public function adminDashboard(VisitorRepository $visitorRepository, Request $request): Response|RedirectResponse
    {
        $user = $this->getUser();
        if (!($user instanceof User)) {
            throw new AccessDeniedException();
        }
        $searchsForm = $passwordForm = $entities = $visitors = null;
        if ($this->session->get('admin_authentification') == null) {

            $passwordForm = $this->createForm(AdminPasswordType::class);
            $passwordForm->handleRequest($request);

            if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {


                 return $this->checkIfIsGranted($passwordForm->get('admin_password')->getData(), $user, $request);
            }

            //accès à l'administration
        } else {
            $visitors = $visitorRepository->findAllVisitor();
            $searchsForm = $this->createForm(SearchsAdminType::class);
            $searchsForm->handleRequest($request);

            if ($searchsForm->isSubmitted() && $searchsForm->isValid()) {
                $params = $searchsForm->get('params_search')->getData();
                $search = $searchsForm->get('search')->getData();
                $entities['entity'] = $params;
                $entities['entities'] = $this->getSearch($search, $params);

            }
        }


        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController', 'admin_password_form' => $passwordForm?->createView(), 'search_form' => $searchsForm?->createView(), 'entities' => $entities, 'visitors' => $visitors
        ]);
    }

    /**
     * @param string $passwordEntered
     * @param User $user
     * @param Request $request
     * @return RedirectResponse
     * Verifie si l'utilisateur a le droit d'accès à l'administration par rapport à l'aide de l'AdminVoter
     */
    public function checkIfIsGranted(string $passwordEntered, User $user, Request $request): RedirectResponse
    {
        if ($this->isGranted('admin_demo', $passwordEntered)) {

            $this->session->set('admin_authentification', 'admin_demo');


            return $this->redirectToRoute('app_admin');

        } elseif ($this->isGranted('admin', $passwordEntered)) {

            $this->session->set('admin_authentification', 'admin');

            return $this->redirectToRoute('app_admin');
        } else {
            if ($this->brutForceChecker->addAdminAttemptFailure($user, $request->getClientIp())) {
                return $this->redirectToRoute('app_logout');
            }
            throw new LogicException();
        }
    }

    /**
     * @param string $search
     * @param string $params
     * @return mixed
     * Récupère les données de l'entité concernée (formulaire)
     */
    public function getSearch(string $search, string $params): mixed
    {

        if ($params == 'Company') {
            return $this->companyRepository->search($search, null);
        } elseif ($params == 'User') {
            return $this->userRepository->searchForAdmin($search);
        } elseif ($params == 'AuthenticationLog') {
            return $this->AuthRepository->searchForAdmin($search);
        }
        throw new LogicException();
    }

    #[Route('/Admin-demo/delete/{uuid}', name: 'app_admin_delete' , defaults: ['public_access' => false])]
    public function deleteEntityForAdmin(string $uuid): RedirectResponse
    {
        if ($this->session->get('admin_authentification') == 'admin'){

            $entity = $this->userRepository->findOneBy(['uuid' => $uuid]);

        if ($entity instanceof User) {
            $this->deleteUser->deleteUser($entity);
        } else {
            $entity = $this->companyRepository->findOneBy(['uuid' => $uuid]);
        }
        if ($entity instanceof Company) {


            $this->deleteCompany->deleteCompany($entity);


        }
    }


        return $this->redirectToRoute('app_admin');
    }
}
