<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $dateTimeZone = new DateTimeZone('Europe/Paris');
        $date = new DateTimeImmutable('now', $dateTimeZone);
        $devUser = new User();
        $devUser->setEmail('Dev@gmail.com')
                ->setRoles(["ROLE_ADMIN"])
                ->setPassword($this->encoder->hashPassword($devUser, 'dev'))
                ->setIsVerified(true)
                ->setLastname('Dev')
                ->setFirstname('Thibault')
                ->setCreatedAt($date);
        $manager->persist($devUser);
        $manager->flush();
    }
}