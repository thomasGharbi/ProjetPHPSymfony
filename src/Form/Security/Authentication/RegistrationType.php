<?php


namespace App\Form\Security\Authentication;

use App\Entity\User;
use App\Form\Security\Authentication\FormExtension\HoneyPotType;
use App\Form\Security\Authentication\FormExtension\RepeatedPasswordType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Unique;


class RegistrationType extends HoneyPotType
{

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array<mixed> $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->add('username', TextType::class, [
            'required' => true,

        ])->add('firstname', TextType::class, [
            'required' => true,

        ])->add('name', TextType::class, [
            'required' => true,

        ])->add('gender', ChoiceType::class, [
            'choices' => [
                'Femme' => 'Femme',
                'Homme' => 'Homme',
                'non précisé' => 'non-précise'
            ]

        ])->add('birth', BirthdayType::class, [
            'widget' => 'choice',
            'input' => 'string',
            'format' => 'dd/MM/yyyy'
        ])
            ->add('phone', TextType::class,
                ['required' => false])
            ->add('email', EmailType::class)
            ->add('password', RepeatedPasswordType::class);


    }


    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}