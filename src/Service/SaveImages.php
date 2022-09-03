<?php

Namespace App\Service;



use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;


class SaveImages{


    public SluggerInterface $slugger;
    public ParameterBagInterface $params;

    public function __construct(SluggerInterface $slugger, ParameterBagInterface $params){
        $this->slugger = $slugger;
        $this->params = $params;

    }

    /**
     * @param mixed $profilImage
     * @param mixed $directory
     * @param mixed $renderDirectory
     * @return string
     */
    public function formateAndSaveImage(mixed $profilImage, mixed $directory, mixed $renderDirectory): string
    {

        $originalFilename = pathinfo($profilImage->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = strval($renderDirectory) . $safeFilename . '-' . uniqid() . '.' . $profilImage->guessExtension();
        $profilImage->move($directory, $newFilename);

        return $newFilename;

    }

    /**
     * @param FormInterface<mixed> $addCompanyForm
     * @return array<string>
     */
    public function uniteImages(FormInterface $addCompanyForm): array
    {

        $imagesFormated = [$this->formateAndSaveImage(
            $addCompanyForm->get('image1')->getData(),
            $this->params->get('app.company_image_directory'),
            $this->params->get('app.company_image_directory_render'))];

        $images = [
            $addCompanyForm->get('image2')->getData(),
            $addCompanyForm->get('image3')->getData(),
            $addCompanyForm->get('image4')->getData(),
            $addCompanyForm->get('image5')->getData(),
        ];


        $images = array_filter($images);

        foreach ($images as $image) {

            if ($image instanceof UploadedFile) {
                $imagesFormated[] = $this->formateAndSaveImage(
                    $image,
                    $this->params->get('app.company_image_directory'),
                    $this->params->get('app.company_image_directory_render'));

            }


        }
        return $imagesFormated;

    }


}