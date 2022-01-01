<?php

namespace App\DataFixtures;



use Faker\Factory;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const  DAYS_MIN_CREATED_AT = '-3days';
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct( UserPasswordHasherInterface $passwordHasher){
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
     $faker = Factory::create('fr_FR');
          
        for ($i = 0; $i < 1; $i++) {
           
            $user = new User();
            
     
            $user->setEmail($faker->email())
                 ->setPassword('badpassword')
                 ->setGender($faker->title())
                 ->setFirstName($faker->firstName())
                 ->setRoles($user->getRoles())
                 ->setName($faker->name())
                 ->setBirth($faker->dayOfMonth() . '/' . $faker->month() . '/' . $faker->year('-18 years'))
                 ->setPhone($faker->phoneNumber())
                 ->setCreatedAt( $this->randomDateBetween($faker->dateTimeBetween($this::DAYS_MIN_CREATED_AT, 'now')->getTimestamp(), (new \DateTime('NOW'))->getTimestamp()))
                 ->setIsVerified(false)
                 ->setAccountMustBeVerifiedBefore(new \DateTimeImmutable('+3 days'))
                 ->setProfilImage($faker->imageUrl(640, 480, 'animals', true));
                 
//New \DateTimeImmutable('2020-02-04T16:00:00')

            $manager->persist($user);
        }

        $manager->flush();
    }


    public function randomDateBetween(int $start, int $end): DateTimeImmutable
    {
          
        $randomDate = mt_rand($start,$end);

        return (new \DateTimeImmutable())->setTimestamp($randomDate);

        
    }
   
    
}