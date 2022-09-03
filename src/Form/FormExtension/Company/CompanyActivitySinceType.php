<?php

namespace App\Form\FormExtension\Company;

use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyActivitySinceType extends AbstractType
{

    private DateTime $time;

    /**
     * @var int[]
     */
    private array $arrayYears;


    public function __construct()
    {
        $this->time = new DateTime('NOW');
        $this->arrayYears = [(int)$this->time->format('Y') => (int)$this->time->format('Y')];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'choices' => $this->addYears($this->arrayYears),

        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }


    /**
     * @param int[] $yearActivity
     * @return array<int>
     */
    private function addYears(array $yearActivity): array
    {

        for ($i = 1; $i <= 100; $i++) {
            foreach ($yearActivity as $key => $year) {

                $key = $key - 1;
                $year = $year - 1;
                $yearActivity += [$key => $year];
            }
        }

        return $yearActivity;
    }


}