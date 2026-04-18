<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        //        $user = User::create('ole@webconsole.de', '');
        //        $user->setPassword($this->passwordHasher->hashPassword($user, 'XS2Test'));
        //        $manager->persist($user);
        //
        //        $manager->flush();
    }
}
