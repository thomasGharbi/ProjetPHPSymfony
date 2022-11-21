<?php


namespace App\Utils;


use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class CustomValidatorForCommand
{
    private UserRepository $userRepository;
    private CompanyRepository $companyRepository;

    /**
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(UserRepository $userRepository, CompanyRepository $companyRepository)
    {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
    }


    public function validateEmail(?string $emailEntered): string
    {
        $emailEntered = $this->isEmpty($emailEntered, "VEUILLEZ SAISIR UN EMAIL");

        if (!filter_var($emailEntered, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("EMAIL SAISI INVALIDE\n");
        }


        if($this->userRepository->findOneBy(['email' => $emailEntered])) {

            throw new InvalidArgumentException("EMAIL SAISI INVALIDE EST DEJA UTILISE");
        }
        return $emailEntered;
    }

    public function validateFirstName(?string $firstNameEntered): string
    {
       $firstNameEntered = $this->isEmpty($firstNameEntered, "LE PRENOM NE PEUT ETRE VIDE");
        $firstNameEntered = strtolower($firstNameEntered);

        return $firstNameEntered;
    }

    public function validateName(?string $nameEntered): string
    {
        $nameEntered = $this->isEmpty($nameEntered, "LE NOM NE PEUT ETRE VIDE");
        $nameEntered = strtolower($nameEntered);
        return $nameEntered;
    }

    public function validateUsername(?string $usernameEntered):string
    {
        $usernameEntered = $this->isEmpty($usernameEntered, "LE NOM D'UTILISATEUR NE PEUT ETRE VIDE");
        $pattern = "/^[A-Za-z][A-Za-z0-9]{5,30}$/";

        if (!preg_match($pattern, $usernameEntered)) {
            throw new InvalidArgumentException("LE NOM D'UTILISATEUR SAISIE N'EST PAS VALIDE :DE 5 A 30 CARACTERES DONT DES LETTRES ET DES CHIFFRES");
        }

        if($this->userRepository->findOneBy(['username' => $usernameEntered])) {

            throw new InvalidArgumentException("LE NOM D'UTILISATEUR SAISI INVALIDE EST DEJA UTILISE");
        }
        return $usernameEntered;
    }

    public function validateBirth(?string $birthEntered): string
    {

        $birthEntered = $this->isEmpty($birthEntered, "LA DATE DE NAISSANCE NE PEUT ETRE VIDE");

        $pattern = "/^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)[1-2][0,9]\d{2}$/";

        if (!preg_match($pattern, $birthEntered)) {
            throw new InvalidArgumentException("LA DATE SAISIE N'EST PAS VALIDE (jj/mm/aaaa)");
        }
        return $birthEntered;
    }

    public function validatePhone(?string $phoneEntered): ?string
    {
        if (!is_null($phoneEntered)) {
            $pattern = "/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/";

            if (preg_match($pattern, $phoneEntered) === 0) {
                throw new InvalidArgumentException("LE NUMERO DE TELEPHONE SAISI N'EST PAS VALIDE");
            }
        }

        return $phoneEntered;
    }

    public function validatePassword(?string $passwordEntered): string
    {
            $passwordEntered = $this->isEmpty($passwordEntered, "LE MOT DE PASSE NE PEUT ETRE VIDE");
            $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

            if (preg_match($pattern, $passwordEntered) === 0) {
                throw new InvalidArgumentException("LE MOT DE PASSE DOIT CONTENIR AU MOINS : 8 CARACTERES DONT DES LETTRES, DES CHIFFRES ET DES CARACTERES SPECIAUX(@$!%*?&)");
            }


        return $passwordEntered;
    }

    /**
     * @param string|null $valu
     * @param string $errorMessage
     * @return string
     */
    private function isEmpty(?string $valu, string $errorMessage): string
    {
        if (empty($valu)) {
            throw new InvalidArgumentException($errorMessage);
        }
        return $valu;
    }



    //---------------------Company--------------------//


    public function validateNameOfCompany(?string $nameOfCompanyEntered): string
    {
        $nameOfCompanyEntered = $this->isEmpty($nameOfCompanyEntered, "LE NOM DE L'ENTREPRISE NE PEUT ETRE VIDE");

        return $nameOfCompanyEntered;
    }

    public function validateSIRETNumber(?string $siretOfCompanyEntered): string
    {
        $siretOfCompanyEntered = $this->isEmpty($siretOfCompanyEntered, "LE NUMERO SIRET DE L'ENTREPRISE NE PEUT ETRE VIDE");

        $pattern = "/^\d{14}$/";

        if (preg_match($pattern, $siretOfCompanyEntered) === 0) {
            throw new InvalidArgumentException("LE NUMERO SIRET DE L'ENTREPRISE N'EST PAS VALIDE (EX : 497154485736158)");
        }

        if($this->companyRepository->findOneBy(['SIRETNumber' => $siretOfCompanyEntered])) {

            throw new InvalidArgumentException("LE NUMERO SIRET SAISI INVALIDE EST DEJA UTILISE PAR UNE AUTRE ENTREPRISE");
        }


        return $siretOfCompanyEntered;
    }

    public function validateNameOfCompanyManager(?string $nameOfCompanyManagerEntered):string
    {
        $nameOfCompanyManagerEntered = $this->isEmpty($nameOfCompanyManagerEntered, "LE NOM DU RESPONSABLE NE PEUT ETRE VIDE");
        $nameOfCompanyManagerEntered = strtolower($nameOfCompanyManagerEntered);
        return $nameOfCompanyManagerEntered;
    }

    public function validateFirstnameOfCompanyManager(?string $firstnameOfCompanyManagerEntered):string
    {

        $firstnameOfCompanyManagerEntered = $this->isEmpty($firstnameOfCompanyManagerEntered, "LE PRENOM DU RESPONSABLE NE PEUT ETRE VIDE");
        $firstnameOfCompanyManagerEntered = strtolower($firstnameOfCompanyManagerEntered);
        return $firstnameOfCompanyManagerEntered;
    }

    public function validateSector(?string $sectorEntered):string
    {
        $sectorEntered = $this->isEmpty($sectorEntered, "LE SECTEUR D'ACTIVITE DE L'ENTREPRISE NE PEUT ETRE VIDE");
        $sectorEntered = strtolower($sectorEntered);
        return $sectorEntered;
    }

    public function validateSpecialization(?string $specializationEntered):?string
    {

        if($specializationEntered !== null){
            $specializationEntered = strtolower($specializationEntered);
        }

        return $specializationEntered;
    }

    public function validateActivitySince(?string $activityYearEntered):string
    {
        $activityYearEntered = $this->isEmpty($activityYearEntered, "L'ANNEE DE DEBUT DE L'ENTREPRISE NE PEUT ETRE VIDE");

        $pattern = "/^\d{4}$/";

        if (preg_match($pattern, $activityYearEntered) === 0) {
            throw new InvalidArgumentException("L'ANNEE DE DEBUT DE L'ENTREPRISE N'EST PAS VALIDE (EX : 2003)");
        }

        return $activityYearEntered;
    }

    public function validateAddressOfCompany(?string $addressOfCompanyEntered):string
    {
        $addressOfCompanyEntered = $this->isEmpty($addressOfCompanyEntered, "L'ADRESSE DE L'ENTREPRISE NE PEUT ETRE VIDE");
         return $addressOfCompanyEntered;
    }

    public function validateDepartmentOfCompany(?string $departmentOfCompanyEntered):string
    {
        $departmentOfCompanyEntered = $this->isEmpty($departmentOfCompanyEntered, "LE DEPARTEMENT DE L'ENTREPRISE NE PEUT ETRE VIDE");
        return $departmentOfCompanyEntered;
    }

    public function validateCityOfCompany(?string $cityOfCompanyEntered):string
    {
        $cityOfCompanyEntered = $this->isEmpty($cityOfCompanyEntered, "LA VILLE DE L'ENTREPRISE NE PEUT ETRE VIDE");
        return $cityOfCompanyEntered;
    }

    public function validatePostalCode(?string $postalCodeOfCompanyEntered):string
    {
        $postalCodeOfCompanyEntered = $this->isEmpty($postalCodeOfCompanyEntered, "LA VILLE DE L'ENTREPRISE NE PEUT ETRE VIDE");


        $pattern = "/^\d{5}$/";

        if (preg_match($pattern, $postalCodeOfCompanyEntered) === 0) {
            throw new InvalidArgumentException("LE CODE POSTAL DE L'ENTREPRISE N'EST PAS VALIDE (EX : 13600)");
        }
        return $postalCodeOfCompanyEntered;
    }

    public function validateProfileTitle(?string $profileTitleEntered):string
    {
        $profileTitleEntered = $this->isEmpty($profileTitleEntered, "LE TITRE DE DESCRIPTION DE L'ENTREPRISE NE PEUT ETRE VIDE");
        return $profileTitleEntered;
    }

    public function validateDescription(?string $descriptionEntered):string
    {
        $descriptionEntered = $this->isEmpty($descriptionEntered, "LA VILLE DE L'ENTREPRISE NE PEUT ETRE VIDE");

        if (strlen($descriptionEntered)  < 30 || strlen($descriptionEntered)  > 250 ) {
            throw new InvalidArgumentException("LA DESCRIPTION DE L'ENTREPRISE N'EST PAS VALIDE (ENTRE 30 ET 250 CARACTERES)");
        }
        return $descriptionEntered;
    }

}