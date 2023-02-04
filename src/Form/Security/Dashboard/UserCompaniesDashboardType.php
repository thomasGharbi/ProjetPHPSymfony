<?php

Namespace App\Form\Security\Dashboard;



use App\Entity\Company;
use App\Form\FormExtension\Company\CompanyActivitySinceType;
use App\Form\FormExtension\Company\CompanyActivityZoneType;
use App\Form\FormExtension\Company\CompanySectorType;
use App\Form\FormExtension\DepartmentType;
use App\Form\FormExtension\ImagesType;
use App\Repository\CompanyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserCompaniesDashboardType extends AbstractType
{
    private CompanyRepository $companyRepository;
    private string $sector;


    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;

    }



    public function buildForm(FormBuilderInterface $builder, array $options):void
    {



         $this->SectorInputManager($options['data']);

        $companyRepository = $this->companyRepository;
        parent::buildForm($builder, $options);

        $builder->add('nameOfCompany', TextType::class,[
            'label' => 'Nom de l\'entreprise :',
            'empty_data' => ''

        ])->add('SIRETNumber', TextType::class,[
            'label' => 'Numéro SIRET :  (12345678912345)',
            'data' => $options['data']->getSiretNumber(),
            'mapped' => false,
            'constraints' => [

                new Callback([
                    'callback' => static function (?string $value, ExecutionContextInterface $context) use ($companyRepository, $options) {

                        if (!empty($value) && $value != $options['data']->getSiretNumber()) {
                            $company = $companyRepository->findBy(['SIRETNumber' => $value]);

                            if ($company) {
                                $context
                                    ->buildViolation("Vous ne pouvez pas utiliser ce numéro SIRET")
                                    ->addViolation();
                            }

                            if (!preg_match('/^\d{14}$/', $value)) {
                                $context
                                    ->buildViolation("le numéro SIRET saisi n'est pas valide")
                                    ->addViolation();
                            }
                        }
                    },
                ]),
            ]
        ])->add('nameOfCompanyManager', TextType::class,[
            'label' => 'Nom du ou des responsable :',
            'empty_data' => ''

        ])->add('firstnameOfCompanyManager', TextType::class,[
            'label' => 'Prénom du ou des responsable :',
            'empty_data' => ''

        ])->add('phone', TextType::class, [
            'label' => 'Numéro de téléphone de l\'entreprise :',

        ])->add('email', EmailType::class, [
            'label' => 'Adresse email de l\'entreprise :',

        ])->add('sector', CompanySectorType::class,[
            'label' => 'Secteur d\'activité :',
            'data' => $this->sector,


        ])->add('otherSector', TextType::class, [
            'label' => 'autres secteur',
            'label_attr' => [
              'id' => 'otherSector'
            ],
            'mapped' => false,
            'data' => $options['data']->getSector() ?? null,
            'constraints' =>
                new Length([
                    'max' => 50,
                    'maxMessage' => "le secteur d'activité ne doit pas dépasser {{ limit }} caractère. "

                ])

        ])->add('specialization', TextType::class,[
            'label' => 'spécialisation (facultatif) :',


        ])->add('inActivitySince', CompanyActivitySinceType::class, [
            'label' => 'Année de création :',
            'empty_data' => ''

        ])->add('address', TextType::class,[
            'label' => 'Adresse :',

        ])->add('department', DepartmentType::class,[
            'label' => 'Département :',

        ])->add('city', TextType::class,[
            'label' => 'Ville :',
            'empty_data' => ''

        ])->add('postalCode', TextType::class,[
            'label' => 'Code postal :',
            'empty_data' => ''


        ])->add('areaActivity', CompanyActivityZoneType::class,[
            'label' => 'Zone maximal d\'activité de l\'entreprise :',
            'data' => $options['data']->getAreaActivity(),

        ])->add('profileImage', ImagesType::class, [
                'data_class' => null,
                'mapped' => false
            ]
        )->add('profileTitle', TextType::class, [
            'label' => 'Titre de présentation de l\'entreprise :',
            'empty_data' => ''


        ])->add('profileDescription', TextAreaType::class, [
            'label' => 'Description de présentation de l\'entreprise :',
            'empty_data' => ''

        ])->add('image1', ImagesType::class, [
            'mapped' => false,
            'data_class' => null
        ])->add('image2', ImagesType::class, [
            'mapped' => false,
            'required' => false,
            'data_class' => null
        ])->add('deleteImage2', CheckboxType::class, [
                'label'    => 'suprimmer l\'image n°2',
                'mapped' => false
        ])->add('image3', ImagesType::class, [
            'mapped' => false,
            'required' => false,
            'data_class' => null
        ])->add('deleteImage3', CheckboxType::class, [
            'label'    => 'suprimmer l\'image n°3',
            'mapped' => false
        ])->add('image4', ImagesType::class, [
            'mapped' => false,
            'required' => false,
            'data_class' => null
        ])->add('deleteImage4', CheckboxType::class, [
            'label'    => 'suprimmer l\'image n°4',
            'mapped' => false
        ])->add('image5', ImagesType::class, [
            'mapped' => false,
            'required' => false,
            'data_class' => null
        ])->add('deleteImage5', CheckboxType::class, [
            'label'    => 'supprimer l\'image n°5',
            'mapped' => false
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false
        ]);
    }

    /**
     * @param Company $options
     * @return void
     */
    public function SectorInputManager(Company $options):void
    {
        $sector = $options->getSector();

        $sectors = [

            'peinture' => 'peinture',
            'echafaudage' => 'echafaudage',
            'plomberie' => 'plomberie',
            'plaquiste plâtrier' => 'plaquiste plâtrier',
            'autre' => 'autre',


        ];

        if (!in_array($sector, $sectors)) {

            $this->sector = 'autre';

        } else {
            $this->sector = $options->getSector();
        }


    }
}