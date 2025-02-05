<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }


    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $password = $this->passwordHasher->hashPassword($user, 'TDuser2k25#');
        $user->setUsername('user')
             ->setEmail('user@todo.com')
             ->setRoles(['ROLE_USER'])
             ->setPassword($password)
        ;

        $manager->persist($user);


        $admin = new User();

        $password = $this->passwordHasher->hashPassword($admin, 'TDadmin2k25#');
        $admin->setUsername('admin')
              ->setEmail('admin@todo.com')
              ->setRoles(['ROLE_ADMIN'])
              ->setPassword($password)
        ;

        $manager->persist($admin);


        $manager->flush();
    }


}
