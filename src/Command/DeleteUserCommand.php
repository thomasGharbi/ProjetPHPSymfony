<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\CustomValidatorForCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;



#[AsCommand(
    name: 'app:delete-user'

)]
class DeleteUserCommand extends Command
{
    private SymfonyStyle $io;
    private CustomValidatorForCommand $validator;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CustomValidatorForCommand $validator,
        UserRepository            $userRepository,
        EntityManagerInterface    $entityManager
    )
    {
        parent::__construct();
        $this->validator = $validator;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;

    }

    protected function Configure(): void
    {
        $this->setDescription('supprimer un utilisateur de la base de donnée')
             ->addArgument('user_email', InputArgument::REQUIRED, "L'email de l'utilisateur qui doit etre supprimé");

    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {

        $this->io->section("SUPPRESSION D'UN UTILISATEUR DE LA BASE DE DONNEE");
        $this->enterEmailUser($input, $output);


        $confirmDelete = $this->io->confirm('ETES VOUS SUR DE VOULOIR SUPPRIMER CETTE UTILISATEUR ?', false);

        if (!$confirmDelete) {

            $this->io->writeln('<fg=blue>ANNULATION DE LA SUPPRESSION DE L\'UTILISATEUR </>');
            die();
        }


    }



    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userEmail = $input->getArgument('user_email');
        $user = $this->userAlreadyExists($userEmail);
        if (!$user) {
            throw new RuntimeException(sprintf("AUCUN UTILISATEUR PRESENT EN BASE DE DONNEES AVEC L'EMAIL SUIVANT: \n $userEmail"));

        }
        $userId = $user->getId();

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->io->writeln("<fg=green>L'UTILISATEUR AYANT L'ID <fg=blue>$userId</> ET L'EMAIL <fg=blue>$userEmail</> A ETAIT <fg=red>SUPPRIME DE LA BASE DE DONNEE</></>");

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * S'occupe de récuperer l'email de l'utilisateur à supprimer via une question
     */
    private function enterEmailUser(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $userEmailQuestion = new Question("ENTREZ <fg=green>L'EMAIL</> DE L'UTILISATEUR QUI DOIT ETRE <fg=red>SUPPRIME</>:  ");
        $userEmailQuestion->setValidator(function ($userEmailEntered) {
            $this->validator->validateEmail($userEmailEntered);
            return $userEmailEntered;
        });
        $userEmailQuestion->setMaxAttempts(5);

        $userEmail = $helper->ask($input, $output, $userEmailQuestion);

        $input->setArgument('user_email', $userEmail);


    }

    /**
     * @param string $email
     * @return User|null
     */
    private function userAlreadyExists(string|null $email): ?User
    {
        return $this->userRepository->findOneBy([
            'email' => $email
        ]);
    }
}
