<?php

namespace App\Form\Security\Dashboard;

use App\Form\FormExtension\ImagesType;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
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


        $builder->add('firstname', TextType::class,[
            'constraints' => new Length(['max' => 50,
                'maxMessage' => "Le prènom ne peut pas contenir moins de {{ limit }} caractères. ",
            ])
        ])
            ->add('name', TextType::class,[
                'constraints' => new Length(['max' => 50,
                    'maxMessage' => "Le nom ne peut pas contenir moins de {{ limit }} caractères. ",
                ])
            ] )
            ->add('gender', ChoiceType::class, [
                'constraints' =>new Length(['max' => 20]),
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Femme' => 'Femme',
                    'Homme' => 'Homme',
                    'non précisé' => 'non-précisé'
                ], 'data' => $options['data']['gender']

            ])->add('birth', BirthdayType::class, [
                'constraints' =>new Length(['max' => 20]),
                'widget' => 'choice',
                'input' => 'string',
                'format' => 'ddMMyyyy',
                'data' => $options['data']['birth']
            ])->add('phone', TextType::class,['empty_data' => ''])
            ->add('email', EmailType::class, [
                'data' => $options['data']['email'],
                'mapped' => false,
                'constraints' => [

                    new Email(['message' => 'Cette adresse email n\'est pas valide']),
                    new Callback([
                        'callback' => static function (?string $value, ExecutionContextInterface $context) use ($rep, $options) {

                            if (!empty($value) && $value != $options['data']['email']) {
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
                'data' => $options['data']['username'],
                'mapped' => false,
                'constraints' => [
                    new Callback([
                        'callback' => static function (?string $value, ExecutionContextInterface $context) use ($rep, $options) {

                            if (!empty($value) && $value !== $options['data']['username']) {
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
            ])->add('profile_image', ImagesType::class, [
              'data_class' => null,
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
}