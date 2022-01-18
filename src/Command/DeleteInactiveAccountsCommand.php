<?php

namespace App\Command;


use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;



#[AsCommand(
    name: 'app:delete-inactive-accounts'

)]
class DeleteInactiveAccountsCommand extends Command
{
    private SymfonyStyle $io;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(

        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;

    }

    protected function Configure(): void
    {
        $this->setDescription('supprime les comptes inactifs de la base de donnee');


    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->DeleteInactiveAccountsSchedulerEvent();

        return Command::SUCCESS;
    }

    private function DeleteInactiveAccountsSchedulerEvent(): void
    {
        //verifie et supprime toutes les minutes les utilisateur n'ayant pas confirmer leurs compte dans le temps donné par la variable "account_must_be_verified_before"
        $sqlQuery = "SET GLOBAL event_scheduler = 1;
                     CREATE DEFINER=`root`@`localhost`
                     EVENT IF NOT EXISTS `Deletes inactive accounts every minute`
                     ON SCHEDULE EVERY 1 MINUTE STARTS NOW() + INTERVAL 1 MINUTE
                     ON COMPLETION PRESERVE ENABLE
                     DO DELETE FROM user
                     WHERE is_verified = false
                     AND account_must_be_verified_before < NOW()";
        $this->entityManager->getConnection()->executeQuery($sqlQuery);

    }

}
