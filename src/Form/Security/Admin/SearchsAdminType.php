<?php

namespace App\Form\Security\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchsAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', searchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',

                ]
            ])->add('params_search', ChoiceType::class, [
                'choices' => [
                    'utilisateurs' => 'User',
                    'Logs de Connexions' => 'AuthenticationLog',
                    'entreprises' => 'Company'],

            ])->add('submitSearch', submitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}