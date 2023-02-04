<?php

namespace App\Twig;

use _PHPStan_76800bfb5\Symfony\Component\Console\Exception\LogicException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RateExtension extends AbstractExtension
{


    public function getFunctions(): array
    {
        return [
            new TwigFunction('rating', [$this, 'ratingEmoji']),
        ];
    }



    public function ratingEmoji(float $value , int $countOfNotice = 0): array
    {

        if($countOfNotice == 0)
        {
            return ['rate' => '➖', 'emoji' => ''];
        }


        if($value <= 2)
        {
            return ['rate' => round($value, 1), 'emoji' => '🤡'];
        }elseif ($value <= 4)
        {
            return ['rate' => round($value, 1), 'emoji' => '☹️'];
        }elseif ($value <= 5)
        {
            return ['rate' => round($value, 1), 'emoji' => '😐'];
        }elseif ($value <= 6)
        {
            return ['rate' => round($value, 1), 'emoji' => '🙂'];
        }elseif ($value <= 9)
        {
            return ['rate' => round($value, 1), 'emoji' => '😃'];
        }elseif ($value <= 10)
        {
            return ['rate' => round($value, 1), 'emoji' => '🤩'];
        }
        else{
            throw New LogicException();
        }
    }



}
