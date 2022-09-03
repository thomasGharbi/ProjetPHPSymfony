<?php

namespace App\Form\Security\Authentication;

use App\Form\FormExtension\RepeatedPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ModifyPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array<mixed> $options
     * @return void
     */
   public function buildForm(FormBuilderInterface $builder, array $options): void
   {
       $builder->add('modifyPassword', RepeatedPasswordType::class);
   }
   
      
   /**
    * configureOptions
    *
    * @param  OptionsResolver $resolver
    * @return void
    */
   public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            
           ]);
    }
}