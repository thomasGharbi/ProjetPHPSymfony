<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DateFormatExtension extends AbstractExtension
{


    public function getFunctions(): array
    {
        return [
            new TwigFunction('date_format', [$this, 'format']),
        ];
    }

    public function format(\DateTime|\DateTimeImmutable $date)
    {
        $now = new \DateTime('NOW');

        $interval = $date->diff($now);


        if($interval->y == 0){
            if($interval->m > 0){
                return "il y a $interval->m mois";
            }elseif ($interval->d > 0){
               $pluriel = $interval->d > 1 ? 's' : null;
                return " il y a $interval->d jour$pluriel";
            }elseif ($interval->h > 0){
                $pluriel = $interval->h > 1 ? 's' : null;
                return " il y a $interval->h heure$pluriel";
            }elseif ($interval->i > 0){
                $pluriel = $interval->i > 1 ? 's' : null;
                return "il y a $interval->i minute$pluriel";
            }else{
                return 'à l\'instant';
            }
        }else{
            return 'le ' . $date->format("d/m/Y");

        }

    }
}
