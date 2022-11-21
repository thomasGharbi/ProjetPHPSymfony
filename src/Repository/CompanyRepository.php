<?php

namespace App\Repository;

use App\Entity\Company;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
* @method Company|null find($id, $lockMode = null, $lockVersion = null)
* @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
* @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {

        parent::__construct($registry, Company::class);
    }

    /**
     * @param string $search
     * @param string|null $params
     * @return mixed
     */
    public function search(string $search, ?string $params):mixed
    {

        $search = str_replace('@', '', $search);

        $query = $this->createQueryBuilder('company');
       // $query->where('a.active = 1');

        if($search !== null){
            $query->andWhere('MATCH_AGAINST(company.nameOfCompany, company.sector, company.specialization, company.department, company.city, company.profileTitle, company.profileDescription, company.SIRETNumber, company.uuid)
             AGAINST (:search boolean)>0')->setParameter('search', $search);

            if($params == 'best_notices'){
                $query = $query->orderBy('company.generalNotice', 'DESC');
            }
            elseif ($params == 'worst_notices'){
                $query = $query->orderBy('company.generalNotice', 'ASC');
            }
            elseif ($params == 'most_notices'){
                $query = $query->orderBy('company.countNotice', 'DESC');
            }


        }
        $query->setMaxResults(100);
        return $query->getQuery()->getResult();
    }

    public function findOneWidthoutParams():mixed
    {

        $query = $this->createQueryBuilder('company');
        $query->select('company')->setMaxResults(1);

        return $query->getQuery()->getOneOrNullResult();
    }



}
