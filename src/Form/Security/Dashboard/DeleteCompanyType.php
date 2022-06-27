<?php

namespace App\Form\Security\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteCompanyType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        parent::buildForm($builder, $options);
        $builder->add('deleteCompany', HiddenType::class, [
            'data' => 'delete-company'
        ]);
    }
}