<?php

namespace App\Form\FormExtension;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver):void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'choices' => self::DEPARTMENTS

        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * @const array|string[]
     *
     */
     const DEPARTMENTS =
        [

            '01-Ain' => 'Ain',
            '02-Aisne' => 'Aisne',
            '03-Allier' => 'Allier',
            '04-Alpes-de-Haute-Provence' => 'Alpes-de-Haute-Provence',
            '05-Hautes-Alpes' => 'Hautes-Alpes',
            '06-Alpes-Maritimes' => 'Alpes-Maritimes',
            '07-Ardèche' => 'Ardèche',
            '08-Ardennes' => 'Ardennes',
            '09-Ariège' => 'Ariège',
            '10-Aube' => 'Aube',
            '11-Aude' => 'Aude',
            '12-Aveyron' => 'Aveyron',
            '13-Bouches-du-Rhône' => 'Bouches-du-Rhône',
            '14-Calvados' => 'Calvados',
            '15-Cantal' => 'Cantal',
            '16-Charente' => 'Charente',
            '17-Charente-Maritime' => 'Charente-Maritime',
            '18-Cher' => 'Cher',
            '19-Corrèze' => 'Corrèze',
            '2A-Corse-du-Sud' => 'Corse-du-Sud',
            '2B-Haute-Corse' => 'Haute-Corse',
            '21-Côte-d\'Or' => 'Côte-d\'Or',
            '22-Côtes d\'Armor' => 'Côtes d\'Armor',
            '23-Creuse' => 'Creuse',
            '24-Dordogne' => 'Dordogne',
            '25-Doubs' => 'Doubs',
            '26-Drôme' => 'Drôme',
            '27-Eure' => 'Eure',
            '28-Eure-et-Loir' => 'Eure-et-Loir',
            '29-Finistère' => 'Finistère',
            '30-Gard' => 'Gard',
            '31-Haute-Garonne' => 'Haute-Garonne',
            '32-Gers' => 'Gers',
            '33-Gironde' => 'Gironde',
            '34-Hérault' => 'Hérault',
            '35-Ille-et-Vilaine' => 'Ille-et-Vilaine',
            '36-Indre' => 'Indre',
            '37-Indre-et-Loire' => 'Indre-et-Loire',
            '38-Isère' => 'Isère',
            '39-Jura' => 'Jura',
            '40-Landes' => 'Landes',
            '41-Loir-et-Cher' => 'Loir-et-Cher',
            '42-Loire' => 'Loire',
            '43-Haute-Loire' => 'Haute-Loire',
            '44-Loire-Atlantique' => 'Loire-Atlantique',
            '45-Loiret' => 'Loiret',
            '46-Lot' => 'Lot',
            '47-Lot-et-Garonne' => 'Lot-et-Garonne',
            '48-Lozère' => 'Lozère',
            '49-Maine-et-Loire' => 'Maine-et-Loire',
            '50-Manche' => 'Manche',
            '51-Marne' => 'Marne',
            '52-Haute-Marne' => 'Haute-Marne',
            '53-Mayenne' => 'Mayenne',
            '54-Meurthe-et-Moselle' => 'Meurthe-et-Moselle',
            '55-Meuse' => 'Meuse',
            '56-Morbihan' => 'Morbihan',
            '57-Moselle' => 'Moselle',
            '58-Nièvre' => 'Nièvre',
            '59-Nord' => 'Nord',
            '60-Oise' => 'Oise',
            '61-Orne' => 'Orne',
            '62-Pas-de-Calais' => 'Pas-de-Calais',
            '63-Puy-de-Dôme' => 'Puy-de-Dôme',
            '64-Pyrénées-Atlantiques' => 'Pyrénées-Atlantiques',
            '65-Hautes-Pyrénées' => 'Hautes-Pyrénées',
            '66-Pyrénées-Orientales' => 'Pyrénées-Orientales',
            '67-Bas-Rhin' => 'Bas-Rhin',
            '68-Haut-Rhin' => 'Haut-Rhin',
            '69-Rhône' => 'Rhône',
            '70-Haute-Saône' => 'Haute-Saône',
            '71-Saône-et-Loire' => 'Saône-et-Loire',
            '72-Sarthe' => 'Sarthe',
            '73-Savoie' => 'Savoie',
            '74-Haute-Savoie' => 'Haute-Savoie',
            '75-Paris' => 'Paris',
            '76-Seine-Maritime' => 'Seine-Maritime',
            '77-Seine-et-Marne' => 'Seine-et-Marne',
            '78-Yvelines' => 'Yvelines',
            '79-Deux-Sèvres' => 'Deux-Sèvres',
            '80-Somme' => 'Somme',
            '81-Tarn' => 'Tarn',
            '82-Tarn-et-Garonne' => 'Tarn-et-Garonne',
            '83-Var' => 'Var',
            '84-Vaucluse' => 'Vaucluse',
            '85-Vendée' => 'Vendée',
            '86-Vienne' => 'Vienne',
            '87-Haute-Vienne' => 'Haute-Vienne',
            '88-Vosges' => 'Vosges',
            '89-Yonne' => 'Yonne',
            '90-Territoire de Belfort' => 'Territoire de Belfort',
            '91-Essonne' => 'Essonne',
            '92-Hauts-de-Seine' => 'Hauts-de-Seine',
            '93-Seine-St-Denis' => 'Seine-St-Denis',
            '94-Val-de-Marne' => 'Val-de-Marne',
            '95-Val-d\'Oise' => 'Val-d\'Oise',
            '971-Guadeloupe' => 'Guadeloupe',
            '972-Martinique' => 'Martinique',
            '973-Guyane' => 'Guyane',
            '974-La Réunion' => 'La Réunion',
            '976-Mayotte' => 'Mayotte'
        ];


}

