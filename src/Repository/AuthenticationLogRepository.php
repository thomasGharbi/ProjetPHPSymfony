<?php

namespace App\Repository;

use App\Entity\AuthenticationLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AuthenticationLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthenticationLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthenticationLog[]    findAll()
 * @method AuthenticationLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthenticationLogRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthenticationLog::class);
    }

    public const DELAY_OF_BLACKLISTING_IN_MINUTES = 10;

    public const MAX_FAILED_AUTH_ATTEMPTS = 5;
    /**
     * @param string $userIp
     * @param string $userEmail
     * @return void
     * Ajoute une Authentification reussi a l'entité AuthenticationLog
     *
     */
    public function addAuthenticationSuccess( string $userIp, ?string $userEmail,bool $oauth = false, ?string $oauthProvider = null):void
{
        $authentication = new AuthenticationLog($userIp, $userEmail, true, $oauth, $oauthProvider);

        $this->_em->persist($authentication);

        $this->_em->flush();

}

    /**
     * @param string $userIp
     * @param string|null $emailEntered
     * @param bool $oauth
     * @param string|null $oauthProvider
     * @param bool $adminAttempt
     * @return \DateTime|null
     * Ajoute une Authentification échoué a l'entité AuthenticationLog
     */
public function addAuthenticationFailure(
    string $userIp,
    ?string $emailEntered,
    bool $oauth = false,
    ?string $oauthProvider = null,
    bool $adminAttempt = false): ?\DateTime
{

    $authentication = new AuthenticationLog($userIp, $emailEntered, false, $oauth, $oauthProvider, $adminAttempt );

    $mustBeBlackListed = $this->getRecentFailureAttempt($userIp);

    //bloque la connexion de L'utilisateur ayant L'adresse ip($userIp) en cas de trop nombreuses tentatives échouées
    if($mustBeBlackListed >= self::MAX_FAILED_AUTH_ATTEMPTS -1)
    {

        $BlackListedDelay = new \DateTime(sprintf('+%d minutes', self::DELAY_OF_BLACKLISTING_IN_MINUTES));
        $authentication->setBlackListed(true)
                       ->setBlackListedUntil($BlackListedDelay);

        $this->_em->persist($authentication);
        $this->_em->flush();

        return $BlackListedDelay;
    }


    $this->_em->persist($authentication);
    $this->_em->flush();
    return null;
}

    /**
     * @param string $userIp
     * @return int
     * Récupère le nombre de connexion échoué par adresse ip
     */
private function getRecentFailureAttempt(string $userIp): int
{
    return $this->createQueryBuilder('AttemptFailure')
                ->select('COUNT(AttemptFailure)')
                ->Where('AttemptFailure.authSuccessful = false')
                ->andWhere('AttemptFailure.authAttemptAt >= :datetime')
                ->andWhere('AttemptFailure.userIp = :userIp')
                ->setParameters([
                              'datetime'      => new \DateTimeImmutable(sprintf('-%d minutes', self::DELAY_OF_BLACKLISTING_IN_MINUTES)),
                              'userIp'        => $userIp,

                ])
                ->getQuery()
                ->getSingleScalarResult();

}

    /**
     * @param string $userIp
     * @return AuthenticationLog|null
     * récupére si il existe le AuthenticationLog dont l'adresse ip est encore bloqué (black listé)
     */
public function getIpBlackListed(string $userIp): ?AuthenticationLog
{
    return  $this->createQueryBuilder('IpBlackListed')
                 ->select('IpBlackListed')
                 ->Where('IpBlackListed.userIp = :userIp')
                 ->andWhere('IpBlackListed.authSuccessful = false')
                 ->andWhere('IpBlackListed.blackListedUntil >= :datetime')
                 ->setParameters([
                     'userIp'        => $userIp,
                     //'datetime'      => new \DateTimeImmutable(sprintf('+%d minutes', self::DELAY_OF_BLACKLISTING_IN_MINUTES))
                     'datetime'      => new \DateTime('NOW')
                 ])
                 ->orderBy('IpBlackListed.blackListedUntil', 'DESC')
                 ->setMaxResults(1)
                 ->getQuery()
                 ->getOneOrNullResult();
}

    /**
     * @param string $search
     * @return mixed
     */
    public function searchForAdmin(string $search):mixed{

        $search = '"' . $search . '"';
        $query = $this->createQueryBuilder('auth');
        $query->andWhere('MATCH_AGAINST(auth.emailEntered, auth.userIp, auth.oauthProvider) AGAINST (:search boolean)>0')->setParameter('search', $search)
           ->setMaxResults(100);

        return $query->getQuery()->getResult();
    }








}
