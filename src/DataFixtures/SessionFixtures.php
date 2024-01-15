<?php

namespace App\DataFixtures;

use App\Entity\Session;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SessionFixtures extends Fixture
{
    public const SESSION_REFERENCE = 'session_ref';

    public function load(ObjectManager $manager): void
    {

        $session = new Session();
        $session->setName('CDA-TC-1123');
        $manager->persist($session);

        $session2 = new Session();
        $session2->setName('DEVWEB-0523');
        $manager->persist($session2);

        
        $this->addReference(self::SESSION_REFERENCE, $session);

        $manager->flush();
    }
}
