<?php

namespace App\Form\Security;

use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class UserDashboardType extends AbstractType
{


    private UserRepository $userRepository;


    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $rep = $this->userRepository;
        parent::buildForm($builder, $options);

        parent::buildForm($builder, $options);
        //dd($options['data']['birth']);
        $builder->add('firstname', TextType::class, ['required' => false])
            ->add('name', TextType::class, ['required' => false])
            ->add('gender', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Femme' => 'Femme',
                    'Homme' => 'Homme',
                    'non précisé' => 'non-précisé'
                ], 'data' => $options['data']['gender']

            ])->add('birth', BirthdayType::class, [
                'widget' => 'choice',
                'input' => 'string',
                'format' => 'dd/MM/yyyy',
                'data' => $options['data']['birth']
            ])->add('phone', TextType::class, ['required' => false])
            ->add('email', EmailType::class, [
                'required' => false,
                'constraints' => [

                    new Callback([
                        'callback' => static function (?string $value, ExecutionContextInterface $context) use ($rep) {

                            if (!empty($value)) {
                                $user = $rep->findBy(['email' => $value]);

                                if ($user) {
                                    $context
                                        ->buildViolation("Cette adresse email est déjà utilisé.")
                                        ->addViolation();
                                }


                            }
                        },
                    ]),
                ]
            ])
            ->add('username', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Callback([
                        'callback' => static function (?string $value, ExecutionContextInterface $context) use ($rep) {

                            if (!empty($value)) {
                                $user = $rep->findBy(['username' => $value]);

                                if ($user) {
                                    $context
                                        ->buildViolation("Ce nom d'utilisateur est déjà utilisé.")
                                        ->addViolation();
                                }

                                if (!preg_match('/^[A-Za-z][A-Za-z0-9]{5,30}$/', $value)) {
                                    $context
                                        ->buildViolation("Votre nom d'utilisateur doit comprendre entre 5 et 30 caractère et contenir uniquement des lettres des chiffres.")
                                        ->addViolation();
                                }
                            }
                        },
                    ]),
                ]
            ])->add('profil_image', FileType::class, [
                'required' => false,
                'constraints' =>
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg,',
                            'image/pjpeg',
                            'image/png',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => 'format de fichier incorrecte (jpg, jpeg, pjpeg, png, gif)',
                        'maxSizeMessage' => 'la taille du fichier est trop grande 2Mo maximum'])
            ]);


    }


}