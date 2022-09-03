<?php

namespace App\Form\FormExtension;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class ImagesType extends AbstractType
{

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' =>
                new Length(['max' => 250,]),
                new File([
                    'maxSize' => '2M',
                    'mimeTypes' => [
                        "image/jpg",
                        "image/jpeg",
                        "image/pjpeg",
                        "image/png",
                        "image/gif"
                    ],
                    'mimeTypesMessage' => 'format de fichier incorrecte (jpg, jpeg, pjpeg, png, gif)',
                    'maxSizeMessage' => 'la taille maximum du fichier et de {{ limit }}M'])]);
    }

    public function getParent(): string
    {
        return FileType::class;
    }

}