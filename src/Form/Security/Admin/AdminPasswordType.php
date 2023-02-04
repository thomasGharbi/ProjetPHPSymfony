<?php

namespace App\Form\Security\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminPasswordType extends AbstractType{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('admin_password', PasswordType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',

                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}