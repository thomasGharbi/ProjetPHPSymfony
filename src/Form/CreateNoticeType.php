<?php
namespace App\Form;
use App\Entity\Notices;
use App\Form\FormExtension\ImagesCRUDType;
use App\Form\FormExtension\ImagesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateNoticeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TextType::class)
                ->add('serviceType',TextType::class)
                ->add('servicePlace', TextType::class)
                ->add('comment', TextAreaType::class)
                ->add('image1', ImagesType::class, [
                'mapped' => false,
                'data_class' => null,
                'constraints' =>[
                    new NotBlank(['message' => 'au minimum une photo doit etre saisi'])]
            ])->add('generalNotice', RangeType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 10
                ],
            ])
              ->add('qualityNotice', RangeType::class,[
                  'attr' => [
                      'min' => 0,
                      'max' => 10
                  ],
              ])
              ->add('speedNotice', RangeType::class , [

                  'attr' => [
                  'min' => 0,
                  'max' => 10
              ],])
              ->add('priceNotice', RangeType::class,[

                  'attr' => [
                      'min' => 0,
                      'max' => 10
                  ],
              ])


            ->add('image2', ImagesType::class, [
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
                'data_class' => null,]);



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Notices::class,
        ]);
    }
}