<?php









namespace App\Form\Security\Authentication;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use App\Form\Security\Authentication\FormExtension\RepeatedPasswordType;




class RegistrationType extends AbstractType
{
     
    /**
     *
     * @param  FormBuilderInterface $builder
     * @param  array<mixed> $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('firstname', TextType::class, [
            
            'required' => true,
            'empty_data' => 'John Doe'
            
              ])->add('name', TextType::class, [
                'required' => true,
                
              ])->add('gender',ChoiceType::class, [
                  'choices' => [
                  'Femme' => 'Femme',
                  'Homme' => 'Homme',
                  'non précisé' => 'non-précisé'
                  ] ,
                  
                      
              ])->add('birth', BirthdayType::class,[
                'widget' => 'choice',
                'input'  => 'string',
                'format' => 'dd-MM-yyyy'
              ])
                ->add('phone', TextType::class)
                ->add('email', EmailType::class,)
                ->add('password', RepeatedPasswordType::class);
              
        
    }

    
        
    /**
     * @param  OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
       $resolver->setDefaults([
        'data_class' => User::class
       ]);
    }
}