<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Concert;
use App\Entity\User;
use App\Repository\ConcertRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

   // Encodage du password pour la fixture des users
   private $encoder;
   private $userRepository;
   private $concertRepository;

   public function __construct(ConcertRepository $concertRepository, UserRepository $userRepository, UserPasswordHasherInterface $encoder)
   {
      $this->encoder = $encoder;
      $this->userRepository = $userRepository;
      $this->concertRepository = $concertRepository;
   }

   // Users fixtures
   public function load(ObjectManager $manager): void
   {
      $faker = Factory::create('fr_FR');

      $user = new User();
      $user->setName('Admin')
         ->setFirstname('Admin')
         ->setEmail('admin@rythmeet.com')
         ->setPseudo('admin.rythmeet')
         ->setBio('Administrateur de RythMeet')
         ->setGender('Non précisé')
         ->setDateOfBirth(new DateTime('2023-03-01'))
         ->setCreatedAt(new DateTime())
         ->setImg('upload/images/users/une-south-park-warcraft.jpg')
         ->setRoles(['ROLE_SUPER_ADMIN']);

         $password = $this->encoder->hashPassword($user, 'rythmeet!');
         $user->setPassword($password);
      
      $manager->persist($user);

      $manager->flush();
   }
}
