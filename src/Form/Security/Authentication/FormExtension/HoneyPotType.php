<?php

namespace App\Form\Security\Authentication\FormExtension;

use App\EventSubscriber\HoneyPotSubscriber;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class HoneyPotType extends AbstractType
//ce honey pot servira a prevenir en cas d'attaque de robot spammer :
// 2 champs vides ne seront pas visible si un ou plusieurs de ces champs
// sont remplit a l'envoi du formulaire c'est potentiellement une attaque spam.
{

    private LoggerInterface $securityLogger;

    private RequestStack $requestStack;

    public function __construct(
        LoggerInterface $securityLogger,
        RequestStack $requestStack
    )
    {
        $this->securityLogger = $securityLogger;
        $this->requestStack = $requestStack;
    }
    //Les constantes ont des noms altéré, car pour google meme si l'attribut autocomplet = off
    // il autocompletera si le nom des champs comporte "adresse" ou "ville" ce qui créera un faux-positif au niveau du HoneyPot
    protected const FAKE_FIELD_FOR_BOT = "adreeess";

    protected  const SECOND_FAKE_FIELD_FOR_BOT = "villle";

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(self::FAKE_FIELD_FOR_BOT, TextType::class,$this->honeyPotFieldConfiguration())
                ->add(self::SECOND_FAKE_FIELD_FOR_BOT, TextType::class,$this->honeyPotFieldConfiguration())
                ->addEventSubscriber(new HoneyPotSubscriber($this->securityLogger, $this->requestStack));
    }

    /**
     * @return array<mixed>
     */
    protected function HoneyPotFieldConfiguration(): array
    {
        //pour ne pas mettre d'attribut "hidden" ou "display-none" qui pourrait compromettre le HoneyPot
        // le style est géré dans public/CSS/app.css
        return [
            'attr' => [
                'autocomplete' => 'off',
                'tabindex' => '-1'
            ],
            'data' => '',
            'mapped' => false,
            'required' => false
        ];
    }
}
