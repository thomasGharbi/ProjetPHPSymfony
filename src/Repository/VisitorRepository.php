<?php

namespace App\Repository;

use App\Entity\Visitor;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Visitor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visitor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visitor[]    findAll()
 * @method Visitor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visitor::class);
    }


    /**
     * @return array<mixed>
     */
    public function findAllVisitor(): array
    {
        $visitors = [];
        $visitors['months'] = $this->findAllMonth();
        $visitors['week'] = $this->findWeek();
        $visitors['day'] = $this->findDay();
        return $visitors;
    }

    /**
     * @return array<mixed>
     */
    public function findAllMonth()
    {
        $dataArray = [];
        $months = [
            '',
            ['month' => 'Janvier'],
            ['month' => 'Février'],
            ['month' => 'Mars'],
            ['month' => 'Avril'],
            ['month' => 'Mai'],
            ['month' => 'Juin'],
            ['month' => 'Juillet'],
            ['month' => 'Aout'],
            ['month' => 'Septembre'],
            ['month' => 'Octobre'],
            ['month' => 'Novembre'],
            ['month' => 'Décembre']
        ];

        for ($i = 0; $i < 12; $i++) {
            //la date moins un mois et 1 jour pour pouvoir récupérer les mois de 30 jours
            $date = new DateTime("-{$i}month -1 day");

            $months[intval($date->format('m'))]['date'] = $date;


            $dataArray[] = $months[intval($date->format('m'))];

        }


        foreach ($dataArray as $key => $month) {


            if ($key == 0) {
                $day = $dataArray[$key]['date']->format('d');
                $dateStart = new DateTime("-$day days");
            } else {
                $lastMonth = $key - 1;
                $dateStart = $dataArray[$lastMonth]['date'];
            }
            $dateEnd = $dataArray[$key]['date'];

            $dataArray[$key]['visitors'] = $this->getQueryDate($dateStart, $dateEnd);


        }


        return $dataArray;

    }


    /**
     * @param DateTime $dateStart
     * @param DateTime $dateEnd
     * @return mixed
     *
     */
    public function getQueryDate(DateTime $dateStart, DateTime $dateEnd): mixed
    {
        return $this->createQueryBuilder('visitor')
            ->select('COUNT(visitor)')
            ->andWhere('visitor.visitedAt >= :date_start')
            ->andWhere('visitor.visitedAt <= :date_end')
            ->setParameter('date_start', $dateStart)
            ->setParameter('date_end', $dateEnd)
            ->getQuery()
            ->getSingleScalarResult();
    }


    /**
     * @return mixed
     *
     */
    public function findWeek(): mixed
    {
        $dateEnd = new DateTime('NOW');
        $dateStart = new DateTime('- 7 days');

        return $this->getQueryDate($dateStart, $dateEnd);
    }

    /**
     * @return mixed
     */
    public function findDay(): mixed
    {
        $dateEnd = new DateTime('NOW');
        $dateStart = new DateTime('- 1 days');

        return $this->getQueryDate($dateStart, $dateEnd);
    }

}
