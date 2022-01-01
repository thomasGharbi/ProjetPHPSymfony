<?php 

namespace App\Form\Security\Authentication;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ForgotPasswordType extends AbstractType{

    /**
     * @param FormBuilderInterface $builder
     * @param array<mixed> $options
     * @return void
     */
    function buildForm(FormBuilderInterface $builder, array $options): void
    {
         $builder
         ->add('email',  EmailType::class,
             [
           'required' => true 
             ]);
    }
}