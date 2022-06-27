<?php

namespace App\Form\Security\Authentication\FormExtension\Company;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyActivityZoneType extends AbstractType
{

    /**
     * @var array|string[]
     */
    private array $sectors = [

        'moins de 10km' => 'moins d 10km',
        '20km' => '20km',
        '30km' => '30km',
        '50km' => '50km',
        '80km' => '80km',
        '150km' => '150km',
        '300km' => '300km',
        'plus de 300km' => 'plus de 300km'


    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'choices' => $this->sectors

        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }


}