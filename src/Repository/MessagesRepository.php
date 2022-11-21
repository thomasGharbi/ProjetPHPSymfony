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
           return $this->createQueryBuilder('messages')
                ->andWhere("messages.conversation = :idConversation")
                ->setParameter('idConversation', $conversation['conversation']->getId())
                ->orderBy('messages.createdAt', 'ASC')
                ->getQuery()
                ->getResult();

    }


    /**
     * @param mixed $conversation
     * @return mixed
     *
     */
    public function MessagesAreUnread(mixed $conversation):mixed
    {

        return $this->createQueryBuilder('messages')
            ->where(" messages.conversation = :uuidConversation")
            ->andWhere("messages.is_read = 0")
            ->setParameter('uuidConversation',$conversation )
            ->getQuery()
            ->getOneOrNullResult();

    }


}
