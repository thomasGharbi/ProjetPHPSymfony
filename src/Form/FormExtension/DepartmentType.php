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

            '01-Ain' => '01-Ain',
            '02-Aisne' => '02-Aisne',
            '03-Allier' => '03-Allier',
            '04-Alpes-de-Haute-Provence' => '04-Alpes-de-Haute-Provence',
            '05-Hautes-Alpes' => '05-Hautes-Alpes',
            '06-Alpes-Maritimes' => '06-Alpes-Maritimes',
            '07-Ardèche' => '07-Ardèche',
            '08-Ardennes' => '08-Ardennes',
            '09-Ariège' => '09-Ariège',
            '10-Aube' => '10-Aube',
            '11-Aude' => '11-Aude',
            '12-Aveyron' => '12-Aveyron',
            '13-Bouches-du-Rhône' => '13-Bouches-du-Rhône',
            '14-Calvados' => '14-Calvados',
            '15-Cantal' => '15-Cantal',
            '16-Charente' => '16-Charente',
            '17-Charente-Maritime' => '17-Charente-Maritime',
            '18-Cher' => '18-Cher',
            '19-Corrèze' => '19-Corrèze',
            '2A-Corse-du-Sud' => '2A-Corse-du-Sud',
            '2B-Haute-Corse' => '2B-Haute-Corse',
            '21-Côte-d\'Or' => '21-Côte-d\'Or',
            '22-Côtes d\'Armor' => '22-Côtes d\'Armor',
            '23-Creuse' => '23-Creuse',
            '24-Dordogne' => '24-Dordogne',
            '25-Doubs' => '25-Doubs',
            '26-Drôme' => '26-Drôme',
            '27-Eure' => '27-Eure',
            '28-Eure-et-Loir' => '28-Eure-et-Loir',
            '29-Finistère' => '29-Finistère',
            '30-Gard' => '30-Gard',
            '31-Haute-Garonne' => '31-Haute-Garonne',
            '32-Gers' => '32-Gers',
            '33-Gironde' => '33-Gironde',
            '34-Hérault' => '34-Hérault',
            '35-Ille-et-Vilaine' => '35-Ille-et-Vilaine',
            '36-Indre' => '36-Indre',
            '37-Indre-et-Loire' => '37-Indre-et-Loire',
            '38-Isère' => '38-Isère',
            '39-Jura' => '39-Jura',
            '40-Landes' => '40-Landes',
            '41-Loir-et-Cher' => '41-Loir-et-Cher',
            '42-Loire' => '42-Loire',
            '43-Haute-Loire' => '43-Haute-Loire',
            '44-Loire-Atlantique' => '44-Loire-Atlantique',
            '45-Loiret' => '45-Loiret',
            '46-Lot' => '46-Lot',
            '47-Lot-et-Garonne' => '47-Lot-et-Garonne',
            '48-Lozère' => '48-Lozère',
            '49-Maine-et-Loire' => '49-Maine-et-Loire',
            '50-Manche' => '50-Manche',
            '51-Marne' => '51-Marne',
            '52-Haute-Marne' => '52-Haute-Marne',
            '53-Mayenne' => '53-Mayenne',
            '54-Meurthe-et-Moselle' => '54-Meurthe-et-Moselle',
            '55-Meuse' => '55-Meuse',
            '56-Morbihan' => '56-Morbihan',
            '57-Moselle' => '57-Moselle',
            '58-Nièvre' => '58-Nièvre',
            '59-Nord' => '59-Nord',
            '60-Oise' => '60-Oise',
            '61-Orne' => '61-Orne',
            '62-Pas-de-Calais' => '62-Pas-de-Calais',
            '63-Puy-de-Dôme' => '63-Puy-de-Dôme',
            '64-Pyrénées-Atlantiques' => '64-Pyrénées-Atlantiques',
            '65-Hautes-Pyrénées' => '65-Hautes-Pyrénées',
            '66-Pyrénées-Orientales' => '66-Pyrénées-Orientales',
            '67-Bas-Rhin' => '67-Bas-Rhin',
            '68-Haut-Rhin' => '68-Haut-Rhin',
            '69-Rhône' => '69-Rhône',
            '70-Haute-Saône' => '70-Haute-Saône',
            '71-Saône-et-Loire' => '71-Saône-et-Loire',
            '72-Sarthe' => '72-Sarthe',
            '73-Savoie' => '73-Savoie',
            '74-Haute-Savoie' => '74-Haute-Savoie',
            '75-Paris' => '75-Paris',
            '76-Seine-Maritime' => '76-Seine-Maritime',
            '77-Seine-et-Marne' => '77-Seine-et-Marne',
            '78-Yvelines' => '78-Yvelines',
            '79-Deux-Sèvres' => '79-Deux-Sèvres',
            '80-Somme' => '80-Somme',
            '81-Tarn' => '81-Tarn',
            '82-Tarn-et-Garonne' => '82-Tarn-et-Garonne',
            '83-Var' => '83-Var',
            '84-Vaucluse' => '84-Vaucluse',
            '85-Vendée' => '85-Vendée',
            '86-Vienne' => '86-Vienne',
            '87-Haute-Vienne' => '87-Haute-Vienne',
            '88-Vosges' => '88-Vosges',
            '89-Yonne' => '89-Yonne',
            '90-Territoire de Belfort' => '90-Territoire de Belfort',
            '91-Essonne' => '91-Essonne',
            '92-Hauts-de-Seine' => '92-Hauts-de-Seine',
            '93-Seine-St-Denis' => '93-Seine-St-Denis',
            '94-Val-de-Marne' => '94-Val-de-Marne',
            '95-Val-d\'Oise' => '95-Val-d\'Oise',
            '971-Guadeloupe' => '971-Guadeloupe',
            '972-Martinique' => '972-Martinique',
            '973-Guyane' => '973-Guyane',
            '974-La Réunion' => '974-La Réunion',
            '976-Mayotte' => '976-Mayotte'
        ];


}

