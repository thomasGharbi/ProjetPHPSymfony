<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateConversationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dataEntities = [];
        foreach ($options['data'] as $entity) {
            if ($entity instanceof User) {
                $dataEntities[$entity->getUsername()] = $entity->getUuid();
            } elseif ($entity instanceof Company) {
                $dataEntities[$entity->getNameOfCompany()] = $entity->getUuid();
            }

        }
        $builder->add('createConversation', buttonType::class, [

        ])
            ->add('talker', choiceType::class, [
                'choices' => $dataEntities

            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
