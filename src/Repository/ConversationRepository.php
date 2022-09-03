<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }


    /**
     * @param array<User|Company> $entities
     * @return mixed
     */
    public function findConversationByDateTime(array $entities): mixed
    {
        $query = $this->createQueryBuilder('conversation');
foreach ($entities as $entity){
    if($entity instanceof User){
        $query = $query->orWhere(":idUser MEMBER OF conversation.users")
            ->setParameter(':idUser', $entity->getId());
    }elseif($entity instanceof Company){
        $query = $query->orWhere(":id{$entity->getId()} MEMBER OF conversation.Companies")
            ->setParameter(":id{$entity->getId()}", $entity->getId());
    }
}


            $query = $query->orderBy('conversation.createdAt', 'ASC')

            ->setMaxResults(100)
            ->getQuery()
            ->getResult();

       return $query ;

    }


    /**
     * @param User|Company $recipient
     * @param User|Company $talker
     * @return  Conversation|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findConversation(User|Company $recipient,User|Company $talker): ?Conversation
    {
        $arrayEntity = [];
        $entities = [
            $recipient,
            $talker
        ];
        foreach ($entities as $entity){
            if($entity instanceof User){
                $arrayEntity[] = ['users', $entity->getId()];
            }elseif($entity instanceof Company){
                $arrayEntity[] = ['Companies', $entity->getId()];
            }
        }

        return $this->createQueryBuilder('conversation')
            ->where(":id MEMBER OF conversation.{$arrayEntity[0][0]} ")
            ->andWhere(":id2 MEMBER OF conversation.{$arrayEntity[1][0]} ")
            ->setParameter('id',$arrayEntity[0][1] )
            ->setParameter('id2',$arrayEntity[1][1])
            ->getQuery()
            ->getOneOrNullResult();


    }

}
