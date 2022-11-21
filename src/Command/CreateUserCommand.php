<?php

namespace App\Command;

use App\Entity\Company;
use App\Entity\User;
use App\Form\FormExtension\Company\CompanyActivityZoneType;
use App\Form\FormExtension\DepartmentType;
use App\Repository\UserRepository;
use App\Service\SendEmail;
use App\Utils\CustomValidatorForCommand;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


#[AsCommand(
    name: 'app:create-user'

)]
class CreateUserCommand extends Command
{


    private SymfonyStyle $io;
    private UserRepository $userRepository;
    private CustomValidatorForCommand $validator;
    private SendEmail $sendEmail;
    private TokenGeneratorInterface $tokenGenerator;
    private EntityManagerInterface $manager;


    private bool $diferentPasswordError = false;

    public function __construct(
        UserRepository            $userRepository,
        CustomValidatorForCommand $validator,
        SendEmail                 $sendEmail,
        TokenGeneratorInterface   $tokenGenerator,
        EntityManagerInterface    $manager,

    )
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->sendEmail = $sendEmail;
        $this->tokenGenerator = $tokenGenerator;
        $this->manager = $manager;



    }

    protected function configure(): void
    {
        $this->setDescription('créer un nouvel utilisateur')
             ->setHelp('création d\'un utilisateur dans la base de donnée ')
             ->addArgument('email', InputArgument::REQUIRED, "L'email de l'utilisateur")
             ->addArgument('first_name', InputArgument::REQUIRED, "Le prènom de l'utilisateur")
             ->addArgument('name', InputArgument::REQUIRED, "le nom de l'utilisateur")
             ->addArgument('username', InputArgument::REQUIRED, "le nom d'utilisateur (username)")
             ->addArgument('birth', InputArgument::REQUIRED, "la date de naissance de l'utilisateur avec le format jj/mm/aaaa")
             ->addArgument('gender', InputArgument::REQUIRED, "le genre de l'utilisateur")
             ->addArgument('phone', InputArgument::REQUIRED, "le numero de telephone de l'utilisateur")
             ->addArgument('role', InputArgument::REQUIRED, "le role de l'utilisateur")
             ->addArgument('password', InputArgument::REQUIRED, "le mot de passe de l'utilisateur")
             ->addArgument('is_verified', InputArgument::REQUIRED, "la verification de l'utilisateur")
             ->addArgument('add_company', InputArgument::OPTIONAL, "ajouter une entreprie")

             ->addArgument('name_of_company', InputArgument::OPTIONAL, "ajouter le nom de l'entreprise")
             ->addArgument('siret_number', InputArgument::OPTIONAL, 'numéro siret de l\'entreprise')
             ->addArgument('name_of_company_manager',InputArgument::OPTIONAL,'nom du responsable de l\'entreprise')
             ->addArgument('firstname_of_company_manager', InputArgument::OPTIONAL,'prenom du responsable de l\'entreprise' )
             ->addArgument('phone_of_company', InputArgument::OPTIONAL, 'le numéro de téléphone de l\'entreprise')
             ->addArgument('email_of_company', InputArgument::OPTIONAL, 'l\'email de l\'entreprise')
             ->addArgument('sector_of_company',InputArgument::OPTIONAL, 'secteur d\'activité de l\'entreprise')
             ->addArgument('specialization_of_company', InputArgument::OPTIONAL, 'specialisation du secteur d\'activité de l\'entreprise')
             ->addArgument('in_activity_since', InputArgument::OPTIONAL, 'début d\'activité de l\'entreprise')
             ->addArgument('address_of_company', InputArgument::OPTIONAL, 'adresse de l\'entreprise')
             ->addArgument('department_of_company', InputArgument::OPTIONAL, 'departement de l\'entreprise')
             ->addArgument('city_of_company',InputArgument::OPTIONAL,'ville de l\'entreprise')
             ->addArgument('postal_code_of_company',InputArgument::OPTIONAL, 'code postal de l\'entreprise' )
             ->addArgument('area_activity_of_company', InputArgument::OPTIONAL, 'zone géographique d\'activité de l\'entreprise')
             ->addArgument('profile_title_of_company', InputArgument::OPTIONAL, 'titre de description de l\'entreprise')
             ->addArgument('description_of_company', InputArgument::OPTIONAL, 'description de l\'entreprise');


    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io->section("AJOUT D'UN UTILISATEUR EN BASE DE DONNEE");

        $this->enterEmail($input, $output)
             ->enterFirstName($input, $output)
             ->enterName($input, $output)
             ->enterUsername($input,$output)
             ->enterBirth($input, $output)
             ->enterGender($input, $output)
             ->enterPhone($input, $output)
             ->enterRole($input, $output)
             ->enterPassword($input, $output)
             ->enterUserIsVerified($input, $output)
             ->addCompany($input,$output);


    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $firstName = $input->getArgument('first_name');
        $name = $input->getArgument('name');
        $username = $input->getArgument('username');
        $birth = $input->getArgument('birth');
        $phone = $input->getArgument('phone');
        $gender = $input->getArgument('gender');
        $role = [$input->getArgument('role')];
        $password = $input->getArgument('password');
        $isVerified = $input->getArgument('is_verified');

        $user = new User();

        $user->setEmail($email)
            ->setFirstName($firstName)
            ->setName($name)
            ->setUsername($username)
            ->setBirth($birth)
            ->setGender($gender)
            ->setRoles($role)
            ->setPassword($password)
            ->setIsVerified($isVerified)
            ->setCreatedAt(new \DateTimeImmutable('NOW'))
            ->setAccountMustBeVerifiedBefore(new \DateTimeImmutable('+ 3days'));

        if ($phone) {
            $user->setPhone($phone);
        }

        if($input->getArgument('siret_number') !== null){
            $company = new Company();
            $company->setNameOfCompany($input->getArgument('name_of_company'))
                    ->setSIRETNumber($input->getArgument('siret_number'))
                    ->setNameOfCompanyManager($input->getArgument('name_of_company_manager'))
                    ->setFirstnameOfCompanyManager($input->getArgument('firstname_of_company_manager'))
                    ->setPhone($input->getArgument('phone_of_company'))
                    ->setEmail($input->getArgument('email_of_company'))
                    ->setSector($input->getArgument('sector_of_company'))
                    ->setSpecialization($input->getArgument('specialization_of_company'))
                    ->setInActivitySince($input->getArgument('in_activity_since'))
                    ->setAddress($input->getArgument('address_of_company'))
                    ->setDepartment($input->getArgument('department_of_company'))
                    ->setCity($input->getArgument('city_of_company'))
                    ->setPostalCode($input->getArgument('postal_code_of_company'))
                    ->setAreaActivity($input->getArgument('area_activity_of_company'))
                    ->setProfileTitle($input->getArgument('profile_title_of_company'))
                    ->setProfileDescription($input->getArgument('description_of_company'));



            $user->addCompany($company);
            $this->manager->persist($company);

        }


        $this->manager->persist($user);
        $this->manager->flush();

        //Creation de l'email de confirmation
        if ($isVerified === false) {

            $UserId = $user->getId();
            $mustBeVerifiedBefor = $user->getAccountMustBeVerifiedBefore();

            if($UserId && $mustBeVerifiedBefor instanceof \DateTimeImmutable )
            {
                $this->sendMailConfirmation($email, $UserId, $mustBeVerifiedBefor);
            }

        }

         $this->io->success("UN NOUVEL UTILISATEUR AVEC L'ADRESSE EMAIL \"$email\" VIENT D'ETRE AJOUTE A LA BASE DE DONNEE");
        return Command::SUCCESS;
    }


    private function enterEmail(InputInterface $input, OutputInterface $output): static
    {
        $messageQuestion = "ENTREZ<fg=green> L'EMAIL </>DE L'UTILISATEUR:   ";
        $email = $this->createQuestion($input, $output, $messageQuestion, 'validateEmail');

        if ($this->userAlreadyExists($email)) {
            throw new RuntimeException(sprintf("L'UTILISATEUR EST DEJA PRESENT EN BASE DE DONNEES AVEC L'EMAIL SUIVANT: \n $email"));

        }

        $input->setArgument('email', $email);
        return $this;
    }

    private function enterFirstName(InputInterface $input, OutputInterface $output): static
    {

        $messageQuestion = "ENTREZ<fg=green> LE PRENOM </>DE L'UTILISATEUR:  ";
        $firstName = $this->createQuestion($input, $output, $messageQuestion, 'validateFirstName');

        $input->setArgument('first_name', $firstName);
        return $this;
    }

    private function enterName(InputInterface $input, OutputInterface $output): static
    {

        $messageQuestion = "ENTREZ<fg=green> LE NOM </>DE L'UTILISATEUR:  ";
        $name = $this->createQuestion($input, $output, $messageQuestion, 'validateName');

        $input->setArgument('name', $name);

        return $this;
    }

    private function enterUsername(InputInterface $input, OutputInterface $output): static
    {
        $messageQuestion = "ENTREZ<fg=green> LE NOM </>D'UTILISATEUR<fg=blue> (username) </>:  ";
        $username = $this->createQuestion($input, $output, $messageQuestion, 'validateUsername');

        $input->setArgument('username', $username);

        return $this;
    }

    private function enterBirth(InputInterface $input, OutputInterface $output): static
    {
        $messageQuestion = "ENTREZ<fg=green> LA DATE DE NAISSANCE </>DE L'UTILISATEUR <fg=blue>(jj/mm/aaaa)</>:  ";
        $birth = $this->createQuestion($input, $output, $messageQuestion, 'validateBirth');

        $input->setArgument('birth', $birth);

        return $this;
    }

    private function enterGender(InputInterface $input, OutputInterface $output): static
    {


        $helper = $this->getHelper('question');
        $genderQuestion = new ChoiceQuestion("VEUILLEZ CHOISIR <fg=green>LE GENRE</> DE L'UTILISATEUR (PAR DEFAULT <fg=blue>\"NON-PRECISE\"</>): ", ['homme', 'femme', 'non-précisé'], 2);
        $genderQuestion->setErrorMessage("LA VALEUR \"%s\" N'EST PAS VALABLE");
        $genderQuestion->setMaxAttempts(5);
        $gender = $helper->ask($input, $output, $genderQuestion);

        $input->setArgument('gender', $gender);
        return $this;
    }

    private function enterPhone(InputInterface $input, OutputInterface $output): static
    {


        $messageQuestion = "ENTREZ <fg=green>LE NUMERO DE TELEPHONE</> DE L'UTILISATEUR <fg=blue>(FACULTATIF)</>:  ";
        $phone = $this->createQuestion($input, $output, $messageQuestion, 'validatePhone');

        $input->setArgument('phone', $phone);

        return $this;
    }

    private function enterRole(InputInterface $input, OutputInterface $output): static
    {
        $rolesList = User::$rolesUserList;
        $helper = $this->getHelper('question');
        $rolesQuestion = new ChoiceQuestion("VEUILLEZ SAISIR <fg=green>LE ROLE</> DE L'UTILISATEUR (PAR DEFAULT <fg=blue>\"$rolesList[0]\"</>):", $rolesList, 0);
        $rolesQuestion->setErrorMessage("LA VALEUR \"%s\" N'EST PAS VALABLE");
        $rolesQuestion->setMaxAttempts(5);
        $role = $helper->ask($input, $output, $rolesQuestion);
        $input->setArgument('role', $role);

        return $this;
    }

    private function enterPassword(InputInterface $input, OutputInterface $output): static
    {

        $messageQuestion = "VEUILLEZ SAISIR <fg=green>LE MOTS DE PASSE</>: ";
        $repeatedMessageQuestion = "VEUILLEZ SAISIR <fg=green>A NOUVEAU</> LE MOT DE PASSE: ";

        if ($this->diferentPasswordError) {
            $messageQuestion = "LES MOTS DE PASSES DOIVENT ETRES IDENTIQUE VEUILLEZ SAISIR A NOUVEAU LES MOTS DE PASSES: ";

        }

        $password = $this->createPasswordQuestion($input, $output, $messageQuestion, 'validatePassword');
        $repeatedPassword = $this->createPasswordQuestion($input, $output, $repeatedMessageQuestion, 'validatePassword');

        //La valeur de la propriété $diferentPasswordError permet de relancer la question si les  2 mots de passes saisi sont different
        if ($password !== $repeatedPassword) {

            $this->diferentPasswordError = true;
            $this->enterPassword($input, $output);

        }
        $this->diferentPasswordError = false;

        $input->setArgument('password', $password);

        return $this;

    }

    private function enterUserIsVerified(InputInterface $input, OutputInterface $output): static
    {
        $role = $input->getArgument('role');

        //si le rôle est défini comme "admin" l'utilisateur sera forcément vérifié
        if ($role === "ROLE_ADMIN") {

            $input->setArgument('is_verified', true);
            return $this;
        }
        $helper = $this->getHelper('question');
        $isVerifiedQuestion = new ChoiceQuestion("VEUILLEZ SAISIR SI <fg=green>L'UTILISATEUR EST VERIFIE</> (PAR DEFAULT <fg=blue>\"VERIFIE\"</>): ", ['non-verifie', 'verifie'], 0);
        $isVerifiedQuestion->setMaxAttempts(5);
        $isVerifiedQuestion->setErrorMessage("LA VALEUR \"%s\" N'EST PAS VALABLE");

        $isVerified = $helper->ask($input, $output, $isVerifiedQuestion);
        $isVerified = $isVerified === 'verifie'; //permet de selectionné le paramètre en utilisant 0 et 1 tout en retournant true ou false

        $input->setArgument('is_verified', $isVerified);
        return $this;

    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $messageQuestion
     * @param string $functionValidator
     * @return string|null
     * crée la logique de création de question
     */
    public function createQuestion(
        InputInterface  $input,
        OutputInterface $output,
        string        $messageQuestion,
        string        $functionValidator): ?string
    {
        $helper = $this->getHelper('question');


        $argumentQuestion = new Question($messageQuestion);
        $argumentQuestion->setValidator([$this->validator, $functionValidator]);
        $argumentQuestion->setMaxAttempts(5);
        $argument = $helper->ask($input, $output, $argumentQuestion);


        return $argument;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $messageQuestion
     * @param string $functionValidator
     * @return string|null
     *crée la logique de création de question pour le mot de passe
     */
    private function createPasswordQuestion(
        InputInterface  $input,
        OutputInterface $output,
        string     $messageQuestion,
        string     $functionValidator): ?string
    {
        $helper = $this->getHelper('question');

        $passwordQuestion = new Question($messageQuestion);
        $passwordQuestion->setValidator([$this->validator, $functionValidator]);
        $passwordQuestion->setHidden(true)
            ->setHiddenFallback(false)
            ->setMaxAttempts(5);
        $password = $helper->ask($input, $output, $passwordQuestion);


        return $password;
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


    /**
     * @param string $email
     * @param int $userId
     * @param \DateTimeImmutable $mustBeVerifiedBefore
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * crée le token de verification et fait apelle au service SendEmail pour envoyer un email de confirmation
     */
    private function sendMailConfirmation(string $email, int $userId, \DateTimeImmutable $mustBeVerifiedBefore): void
    {
        $registrationToken = $this->tokenGenerator->generateToken();
        $this->sendEmail->send([
            'recipient' => $email,
            'subject' => "vérification de votre compte",
            'html_template' => "Security/Authentication/email/registrationEmail.html.twig",
            'context' => [
                'userID' => $userId,
                'registrationToken' => $registrationToken,
                'tokenDuration' => $mustBeVerifiedBefore->format('d/m/Y à H:i')
            ]
        ]);
    }


    public function addCompany(InputInterface $input, OutputInterface $output):static
    {

        $helper = $this->getHelper('question');
        $addCompanyQuestion = new ChoiceQuestion("VOULEZ-VOUS AJOUTER UNE <fg=green>ENTREPRISE</> (PAR DEFAULT <fg=blue>\"Oui\"</>): ", ['oui', 'non'], 0);
        $addCompanyQuestion->setMaxAttempts(5);
        $addCompanyQuestion->setErrorMessage("LA VALEUR \"%s\" N'EST PAS VALABLE");

        $addCompany = $helper->ask($input, $output, $addCompanyQuestion);
        $addCompany = $addCompany === 'oui';

        if(!$addCompany){
            return $this;
        }
        $this->enterNameOfCompany($input, $output)
             ->enterSIRETNumber($input, $output)
             ->enterNameOfCompanyManager($input, $output)
             ->enterFirstnameOfCompanyManager($input, $output)
             ->enterPhoneOfCompany($input,$output)
             ->enterEmailOfCompany($input, $output)
             ->enterSectorOfCompany($input, $output)
             ->enterSpecializationOfCompany($input, $output)
             ->enterInActivitySince($input, $output)
             ->enterAddressOfCompany($input, $output)
             ->enterDepartmentOfCompany($input, $output)
             ->enterCityOfCompany($input, $output)
             ->enterPostalCode($input, $output)
             ->enterAreaActivityOfCompany($input, $output)
             ->enterProfileTitleOfCompany($input, $output)
             ->enterDescriptionOfCompany($input, $output);

return $this;

    }

    private function enterNameOfCompany(InputInterface $input, OutputInterface $output):static
    {

        $messageQuestion = "ENTREZ<fg=green> LE NOM </>DE L'ENTREPRISE:  ";
        $nameOfCompany = $this->createQuestion($input, $output, $messageQuestion, 'validateNameOfCompany');

        $input->setArgument('name_of_company', $nameOfCompany);
        return $this;

    }

    private function enterSIRETNumber(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> LE NUMERO SIRET </>DE L'ENTREPRISE:  ";
        $SIRETNumber = $this->createQuestion($input, $output, $messageQuestion, 'validateSIRETNumber');

        $input->setArgument('siret_number', $SIRETNumber);
        return $this;
    }

    private function enterNameOfCompanyManager(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> LE NOM DU RESPONSABLE </>DE L'ENTREPRISE:  ";
        $nameOfCompanyManager = $this->createQuestion($input, $output, $messageQuestion, 'validateNameOfCompanyManager');
        $input->setArgument('name_of_company_manager', $nameOfCompanyManager);
        return $this;
    }

    private function enterFirstnameOfCompanyManager(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> LE PRENOM DU RESPONSABLE </>DE L'ENTREPRISE:  ";
        $firstnameOfCompanyManager = $this->createQuestion($input, $output, $messageQuestion, 'validateFirstnameOfCompanyManager');
        $input->setArgument('firstname_of_company_manager', $firstnameOfCompanyManager);
        return $this;
    }

    private function enterPhoneOfCompany(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> LE NUMERO DE TELEPHONE </>DE L'ENTREPRISE <fg=blue>(FACULTATIF)</>:  ";
        $phoneOfCompany = $this->createQuestion($input, $output, $messageQuestion, 'validatePhone');
        $input->setArgument('phone_of_company', $phoneOfCompany);
        return $this;
    }

    private function enterEmailOfCompany(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> L'EMAIL  </>DE L'ENTREPRISE:  ";
        $emailOfCompany = $this->createQuestion($input, $output, $messageQuestion, 'validateEmail');
        $input->setArgument('email_of_company', $emailOfCompany);
        return $this;
    }

    private function enterSectorOfCompany(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> LE SECTEUR D'ACTIVITE </>DE L'ENTREPRISE:  ";
        $sectorOfCompany = $this->createQuestion($input, $output, $messageQuestion, 'validateSector');
        $input->setArgument('sector_of_company', $sectorOfCompany);

        return $this;
    }

    private function enterSpecializationOfCompany(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> LA SPECIALISATION </>DE L'ENTREPRISE <fg=blue>(FACULTATIF)</>:  ";
        $specializationOfCompany = $this->createQuestion($input, $output, $messageQuestion, 'validateSpecialization');
        $input->setArgument('specialization_of_company', $specializationOfCompany);

        return $this;
    }

    private function enterInActivitySince(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> L'ANNEE </>DE DEBUT DE LA CREATION DE L'ENTREPRISE :  ";
        $inActivitySince = $this->createQuestion($input, $output, $messageQuestion, 'validateActivitySince');
        $input->setArgument('in_activity_since', $inActivitySince);

        return $this;
    }

    private function enterAddressOfCompany(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> L'ADRESSE </> DE L'ENTREPRISE :  ";
        $addressOfCompany = $this->createQuestion($input, $output, $messageQuestion, 'validateAddressOfCompany');
        $input->setArgument('address_of_company', $addressOfCompany);

        return $this;
    }

    private function enterDepartmentOfCompany(InputInterface $input, OutputInterface $output):static
    {
        $helper = $this->getHelper('question');
        $departmentOfCompanyQuestion = new ChoiceQuestion("AJOUTEZ LE DEPARTEMENT DE L'ENTREPRISE (PAR DEFAULT <fg=blue>\"" . array_values(DepartmentType::DEPARTMENTS)[0] . "\"</>) : ", array_values(DepartmentType::DEPARTMENTS), 0);
        $departmentOfCompanyQuestion->setMaxAttempts(5);
        $departmentOfCompanyQuestion->setErrorMessage("LA VALEUR \"%s\" N'EST PAS VALABLE");

        $departmentOfCompany = $helper->ask($input, $output, $departmentOfCompanyQuestion);
        $input->setArgument('department_of_company', $departmentOfCompany);

        return $this;
    }

    private function enterCityOfCompany(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> LA VILLE </> DE L'ENTREPRISE :  ";
        $cityOfCompany = $this->createQuestion($input, $output, $messageQuestion, 'validateCityOfCompany');
        $input->setArgument('city_of_company', $cityOfCompany);

        return $this;
    }

    private function enterPostalCode(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> LE CODE POSTAL </> DE L'ENTREPRISE :  ";
        $postalCodeOfCompany = $this->createQuestion($input, $output, $messageQuestion, 'validatePostalCode');
        $input->setArgument('postal_code_of_company', $postalCodeOfCompany);

        return $this;
    }

    private function enterAreaActivityOfCompany(InputInterface $input, OutputInterface $output):static
    {

        $helper = $this->getHelper('question');
        $areaActivityQuestion = new ChoiceQuestion("AJOUTEZ UNE ZONE MAXIMALE D'ACTIVITE  (PAR DEFAULT <fg=blue>\"" . array_values(CompanyActivityZoneType::ACTIVITY_ZONE)[0] . "\"</>):   ", array_values(CompanyActivityZoneType::ACTIVITY_ZONE), 0 );
        $areaActivityQuestion->setMaxAttempts(5);
        $areaActivityQuestion->setErrorMessage("LA VALEUR \"%s\" N'EST PAS VALABLE");

        $areaActivity = $helper->ask($input, $output, $areaActivityQuestion);
        $input->setArgument('area_activity_of_company', $areaActivity);

        return $this;

    }

    private function enterProfileTitleOfCompany(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> LE TITRE DESCRIPTIF </> DE L'ENTREPRISE :  ";
        $profileTitleOfCompany = $this->createQuestion($input, $output, $messageQuestion, 'validateProfileTitle');
        $input->setArgument('profile_title_of_company', $profileTitleOfCompany);

        return $this;
    }

    private function enterDescriptionOfCompany(InputInterface $input, OutputInterface $output):static
    {
        $messageQuestion = "ENTREZ<fg=green> LA DESCRIPTION </> DE L'ENTREPRISE :  ";
        $descriptionOfCompany = $this->createQuestion($input, $output, $messageQuestion, 'validateDescription');
        $input->setArgument('description_of_company', $descriptionOfCompany);

        return $this;
    }



}
