<?php


namespace App\Utils;





use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CreateRandomUsername extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);


    }

    public function createRandomUsername(): string
    {
        $username = str_pad('utilisateur' . random_int(0, 99999), 5, "0", STR_PAD_LEFT);

        $username = $this->checkUsernameExist($username);

        return $username;


    }

    public function checkUsernameExist(string $username): string
    {
        $user = $this->findOneBy(['username' => $username]);

        if($user){
            $this->createRandomUsername();
        }
        return $username;
    }
}