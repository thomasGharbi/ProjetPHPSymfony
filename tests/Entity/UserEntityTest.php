<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserEntityTest extends KernelTestCase
{

    private const EMAIL_NOT_BLANK_MESSAGE = 'L\'adresse email doit être saisi';
    private const PASSWORD_NOT_BLANK_MESSAGE = 'Le mot de passe doit être saisi';
    private const USERNAME_NOT_BLANK_MESSAGE = 'Le nom d\'utilisateur doit être saisi';

    private const EMAIL_INVALID_MESSAGE = 'Cette adresse email n\'est pas valide';
    private const PASSWORD_INVALID_MESSAGE = 'Le mot de passe doit contenir au moins: huit caractères dont une lettre, un chiffre et un caractère spécial(@$!%*?&)';
    private const USERNAME_INVALID_MESSAGE = 'Votre nom d\'utilisateur doit comprendre entre 5 et 30 caractère et contenir uniquement des lettres des chiffres';

    private const EMAIL_EXIST_MESSAGE = 'Cette adresse email est déjà associé à un compte existant';
    private const PHONE_EXIST_MESSAGE = 'Ce numéro de téléphone est déjà associé à un compte existant';
    private const USERNAME_EXIST_MESSAGE = 'Ce nom d\'utilisateur est déjà pris';


    public ValidatorInterface $validator;

    protected function  setUp(): void
    {
        $kernel = self::bootKernel();

        $this->validator = $kernel->getContainer()->get('validator');

    }

    public function testUserEntityWidthBlankValues():void
{

    $user = new User;

    $user->setEmail('')
         ->setUsername('')
         ->setPassword('');
    $this->getValidationNotBlankErrors($user);

}

public function getValidationNotBlankErrors(User $user):ConstraintViolationList
{

    $errors = $this->validator->validate($user);

    $this->assertEquals(self::EMAIL_NOT_BLANK_MESSAGE, $errors[0]->getMessage());
    $this->assertEquals(self::USERNAME_NOT_BLANK_MESSAGE, $errors[1]->getMessage());
    $this->assertEquals(self::PASSWORD_NOT_BLANK_MESSAGE, $errors[2]->getMessage());

}

    public function testUserEntityWidthInvalidValues():void
    {

        $user = new User;

        $user->setEmail('this_is_not_an_email')
             ->setUsername('bad_password')
             ->setPassword('bad_username@@@');
        $this->getValidationInvalidErrors($user);

    }


    public function getValidationInvalidErrors($user):ConstraintViolationList
    {

        $errors = $this->validator->validate($user);

        $this->assertEquals(self::EMAIL_INVALID_MESSAGE, $errors[0]->getMessage());
        $this->assertEquals(self::USERNAME_INVALID_MESSAGE, $errors[1]->getMessage());
        $this->assertEquals(self::PASSWORD_INVALID_MESSAGE, $errors[2]->getMessage());



    }


    public function testUserEntityWidthExistValues():void
    {

        //créer un utilisateur via la command app:create-user avec
        // l'email : security@test.com et comme nom d'utilisateur : username1234 et avec comme numéro de téléphone 0601020304
        $user = new User;

        $user->setEmail('security@test.com')
             ->setUsername('username1234')
             ->setPassword('Password!1234')
             ->setPhone('0601020304');
        $this->getValidationExistErrors($user);

    }


    public function getValidationExistErrors($user):ConstraintViolationList
    {

        $errors = $this->validator->validate($user);

        $this->assertEquals(self::EMAIL_EXIST_MESSAGE, $errors[0]->getMessage());
        $this->assertEquals(self::PHONE_EXIST_MESSAGE, $errors[1]->getMessage());
        $this->assertEquals(self::USERNAME_EXIST_MESSAGE, $errors[2]->getMessage());



    }

    public function testUserEntityWidthRightValues():void
    {

        //créer un utilisateur via la command app:create-user avec
        // l'email : security@test.com et comme nom d'utilisateur : username1234 et avec comme numéro de téléphone 0601020304
        $user = new User;

        $user->setEmail('security-right@test.com')
            ->setUsername('username5846')
            ->setPassword('Password!1234')
            ->setPhone('0640121213');
        $this->getValidationNoErrors($user);

    }


    public function getValidationNoErrors($user):void
    {
        $errors = $this->validator->validate($user);

        $this->assertEmpty($errors);
    }



}