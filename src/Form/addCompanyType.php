<?php

namespace App\Form;


use App\Entity\Company;
use App\Form\FormExtension\Company\CompanyActivitySinceType;
use App\Form\FormExtension\Company\CompanyActivityZoneType;
use App\Form\FormExtension\Company\CompanySectorType;
use App\Form\FormExtension\DepartmentType;
use App\Form\FormExtension\ImagesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class addCompanyType extends AbstractType {


    /**
     * @param FormBuilderInterface $builder
     * @param array<mixed> $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('nameOfCompany', TextType::class,[
            'label' => 'Nom de l\'entreprise :'
        ])->add('SIRETNumber', TextType::class,[
            'label' => 'Numéro SIRET :  (12345678912345)'
        ])->add('nameOfCompanyManager', TextType::class,[
            'label' => 'Nom du ou des responsable :'
        ])->add('firstnameOfCompanyManager', TextType::class,[
            'label' => 'Prénom du ou des responsable :'
        ])->add('phone', TextType::class, [
            'label' => 'Numéro de téléphone de l\'entreprise :'
        ])->add('email', EmailType::class, [
            'label' => 'Adresse email de l\'entreprise :'
        ])->add('sector', CompanySectorType::class,[
            'label' => 'Secteur d\'activité :',

        ])->add('otherSector', TextType::class, [
            'label_attr' => [
                'id' => 'otherSector'
            ],
            'mapped' => false,
            'required' => false,
            'constraints' =>[
               new Length([
                   'max' => 50,
                   'maxMessage' => "le secteur d'activité ne doit pas dépasser {{ limit }} caractère. "

               ])
            ]

        ])->add('specialization', TextType::class,[
            'label' => 'spécialisation (facultatif) :',
            'required' => false
        ])->add('inActivitySince', CompanyActivitySinceType::class, [
            'label' => 'Année de création :'
        ])->add('address', TextType::class,[
            'label' => 'Adresse :'
        ])->add('department', DepartmentType::class,[
            'label' => 'Département :',
            'required' => true
        ])->add('city', TextType::class,[
            'label' => 'Ville :'
        ])->add('postalCode', TextType::class,[
            'label' => 'Code postal :'

        ])->add('areaActivity', CompanyActivityZoneType::class,[
            'label' => 'Zone maximal d\'activité de l\'entreprise :'
        ])->add('profileImage', ImagesType::class,[
            'data_class' => null,
            'required' => true
            ])->add('profileTitle', TextType::class, [
            'label' => 'Titre de présentation de l\'entreprise :'
        ])->add('profileDescription', TextareaType::class, [
            'label' => 'Description de présentation de l\'entreprise :'
        ])->add('image1', ImagesType::class, [
            'mapped' => false,
            'data_class' => null,
            'constraints' =>[
                new NotBlank(['message' => 'au minimum une photo doit etre saisi'])]
        ])->add('image2', ImagesType::class, [
            'mapped' => false,
            'required' => false,
            'data_class' => null,
        ])->add('image3', ImagesType::class, [
            'mapped' => false,
            'required' => false,
            'data_class' => null,
        ])->add('image4', ImagesType::class, [
            'mapped' => false,
            'required' => false,
            'data_class' => null,
        ])->add('image5', ImagesType::class, [
            'mapped' => false,
            'required' => false,
            'data_class' => null,

        ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class
        ]);
    }
}