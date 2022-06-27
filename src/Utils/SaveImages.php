<?php

Namespace App\Utils;

use Symfony\Component\String\Slugger\SluggerInterface;


class SaveImages{


    public SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger){
        $this->slugger = $slugger;
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

}