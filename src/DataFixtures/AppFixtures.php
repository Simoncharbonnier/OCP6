<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Trick;
use Monolog\DateTimeImmutable;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('Simon');
        $user->setMail('simoncharbonnier@orange.fr');
        $user->setPassword($this->hasher->hashPassword($user, 'secret'));
        $user->setEnabled(true);
        $manager->persist($user);

        for ($i = 0; $i < 20; $i++) {
            $trick = new Trick();
            $trick->setName('Trick '.$i);
            $trick->setDescription('Trick '.$i.' : DESCRIPTION');
            $trick->setUser($user);
            $trick->setCreatedAt(new DateTimeImmutable('now'));
            $trick->setUpdatedAt(new DateTimeImmutable('now'));
            $manager->persist($trick);
        }

        $manager->flush();
    }
}
