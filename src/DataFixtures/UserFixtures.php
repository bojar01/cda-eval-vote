<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public const SESSION_REFERENCE = 'session_ref';

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();

        for($i = 0; $i< 10 ; $i++) {
            $user = new User();
            $user->setEmail('test'.$i.'@email.com');
            $user->setPassword($this->passwordHasher->hashPassword($user, 'test'));
            $user->setFirstname('test'. $i);
            $user->setLastname('test'. $i);

            $user->setSession($this->getReference(SessionFixtures::SESSION_REFERENCE));

            $manager->persist($user);
        }


        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            SessionFixtures::class
        );
    }
}
