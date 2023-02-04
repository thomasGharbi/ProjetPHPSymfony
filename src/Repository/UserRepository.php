<?php

namespace App\Repository;

use App\Entity\User;
use App\Utils\CreateRandomUsername;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<User>
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{    
    private CreateRandomUsername $createRandomUsername;

    public function __construct(ManagerRegistry $registry, CreateRandomUsername $createRandomUsername)
    {
        parent::__construct($registry, User::class);
        $this->createRandomUsername = $createRandomUsername;

    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param string $provider
     * @param string $OAuthID
     * @param string $email
     * @return User|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * Verifie si un utilisateur corespondant a l'adresse email existe deja et si il a un compte OAuth relié
     */
    public function getUserFromOAuth(
        string $provider,
        string $OAuthID,
        string $email
    ): ?User
    {


       $user = $this->findOneBy(['email' => $email]);

       if(!$user){
           return null;
       }


       if ($provider == 'google' && $user->getGoogleID() !== $OAuthID) {

           $user->setGoogleID($OAuthID);

           $this->_em->flush();
       }

        if ($provider == 'github' && $user->getGithubID() !== $OAuthID) {

           $user->setGithubID($OAuthID);

           $this->_em->flush();
        }


        return $user;
    }


    /**
     * @param string $provider
     * @param array<mixed> $OAuthUserData
     */
    public function createUserFromOAuth(
        string $provider,
        array $OAuthUserData
    ): User
    {
        $user = new User();


        $user->setIsVerified($OAuthUserData['verified'])
             ->setEmail($OAuthUserData['email'])
             ->setPassword($OAuthUserData['random_password'])
             ->setGender('non-précisé')
             ->setCreatedAt(new \DateTimeImmutable('NOW'))
             ->setUsername($this->createRandomUsername->createRandomUsername());

        if($provider == 'google'){
            $user->setGoogleID($OAuthUserData['oauth_id'])
                 ->setFirstName($OAuthUserData['first_name'])
                 ->setName($OAuthUserData['name']);
        }elseif ($provider == 'github'){
            $user->setGithubID($OAuthUserData['oauth_id']);
        }

        if(array_key_exists('must_be_verified_before', $OAuthUserData) && $OAuthUserData['must_be_verified_before'] instanceof \DateTimeImmutable && $OAuthUserData['validation_Token'])
        {
            $user->setAccountMustBeVerifiedBefore($OAuthUserData['must_be_verified_before'])
                 ->setRegistrationToken($OAuthUserData['validation_Token']);
        }

        $this->_em->persist($user);
        $this->_em->flush();

        return $user;
    }

    /**
     * @param string $search
     * @return mixed
     */
    public function searchForAdmin(string $search):mixed
    {
        $search = '"' . $search . '"';
        $query = $this->createQueryBuilder('user');
        $query->andWhere('MATCH_AGAINST(user.email, user.username, user.googleID, user.githubID, user.phone, user.uuid)
             AGAINST (:search boolean)>0')->setParameter('search', $search)
            ->setMaxResults(100);
        return $query->getQuery()->getResult();
    }

    public function findOneWidthoutParams():mixed
    {

        $query = $this->createQueryBuilder('user');
        $query->select('user')->setMaxResults(1);

        return $query->getQuery()->getOneOrNullResult();
    }

}
