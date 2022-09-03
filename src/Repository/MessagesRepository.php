<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Messages;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Messages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messages[]    findAll()
 * @method Messages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);
    }

     /**
      * @return Messages[] Returns an array of Messages objects
      */

    /**
     * @param array<mixed> $conversation
     * @return array<mixed>
     */
    public function findMessagesByDateTime(array $conversation): ?array
    {
$arrayConversation = [];
foreach ($conversation as $entity){
    if($entity instanceof User){
        $arrayConversation[] = ['userOwner', $entity];
    }elseif($entity instanceof Company){
        $arrayConversation[] = ['companyOwner', $entity];
    }
}

           return $this->createQueryBuilder('messages')
                ->where(" messages.{$arrayConversation[0][0]} = :id")
                ->orWhere(" messages.{$arrayConversation[1][0]} = :id2")
                ->andWhere("messages.conversation = :idConversation")
                ->setParameter('id',$arrayConversation[0][1] )
                ->setParameter('id2',$arrayConversation[1][1])
                ->setParameter('idConversation', $conversation['conversation']->getId())
                ->orderBy('messages.createdAt', 'ASC')
                //->setMaxResults(10)
                ->getQuery()
                ->getResult();


    }




}
