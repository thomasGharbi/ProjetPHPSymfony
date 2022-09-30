<?php

namespace App\Service;


use App\Entity\User;
use App\Entity\Visitor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\LogicException;
use Symfony\Component\Security\Core\Security;


class AddVisitor
{
    private SessionInterface $session;
    private Security $security;
    private RequestStack $request;
    private EntityManagerInterface $entityManager;
    /**
     * @var array|string[]
     */
    private array $addVisitorType = [
        'visitor_main_search',
        'visitor_main',
        'visitor_company',
        'visitor_registration',
        'visitor_login',
        'visitor_is_authenticated'];


    public function __construct(SessionInterface $session, Security $security, RequestStack $request, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->security = $security;
        $this->request = $request;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $actionTypeVisitor
     * @param int $point
     * @return void
     *
     * plusieurs checkpoints ($this->addVisitorType) sont placés dans le code si plusieurs checkpoints sont passés le visiteur est validé
     */
    public function addPointToVisitor(string $actionTypeVisitor, int $point): void
    {

        if ($this->session->get($actionTypeVisitor) == null) {

            $this->session->set($actionTypeVisitor, $point);
        }

        $this->checkVisitor();

    }

    /**
     * @return void
     */
    public function checkVisitor(): void
    {
        $points = null;
        $actions = [];

        //vérifie si le visiteur à déja était enregistré

        if ($this->session->get('visitor_added') == null) {

            foreach ($this->addVisitorType as $type) {

                if (is_integer($this->session->get($type))) {
                    $points += $this->session->get($type);
                    $actions[] = $type;
                }
            }
            if ($points >= 3) {
                $this->session->set('visitor_added', 1);
                $visitor = new Visitor();
                $visitor->setUserIp($this->request->getCurrentRequest()?->getClientIp())
                    ->setActions($actions);
                if (in_array('visitor_is_authenticated', $actions)) {

                    /**
                     * @var User|null
                     */
                    $user = $this->security->getUser();

                    if (!($user instanceof User)) {
                        throw new LogicException();
                    }
                    $visitor->setUser($user);
                }
                $this->entityManager->persist($visitor);
                $this->entityManager->flush();

            }


        }
    }
}