<?php


namespace App\Utils;


use Symfony\Component\Console\Exception\InvalidArgumentException;

class CustomValidatorForCommand
{

    public function validateEmail(?string $emailEntered): string
    {
        $emailEntered = $this->isEmpty($emailEntered, "VEUILLEZ SAISIR UN EMAIL");

        if (!filter_var($emailEntered, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("EMAIL SAISI INVALIDE\n");
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
                throw new InvalidArgumentException("LE MOT DE PASSE DOIT CONTENIR AU MOINS : 8 CARACTERES DONT UNE LETTE, UN CHIFFRE ET UN CARACTERE SPECIAL(@$!%*?&)");
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



}