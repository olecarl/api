<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Support\UnitTester;
use Codeception\Test\Unit;

class UserTest extends Unit
{
    public const string EMAIL = 'test@webconsole.de';

    public const string PASSWORD = 'XS2Test';

    protected UnitTester $tester;

    protected function _before(): void
    {
    }

    public function testCreateUser(): void
    {
        $user = User::create(self::EMAIL, self::PASSWORD);
        $this->assertSame(User::ROLE_USER, $user->getRoles()[0]);
        $this->assertFalse($user->isVerified());
    }

    public function testSaveValidUser(): void
    {
        $user = new User();
        $user->setEmail(self::EMAIL);
        $container = $this->getModule('Symfony')->_getContainer();
        $passwordHasher = $container->get('security.password_hasher');
        $password = $passwordHasher->hashPassword($user, self::PASSWORD);
        $user->setPassword($password);

        /** @var UserRepository $users */
        $users = $container->get(UserRepository::class);
        $users->save($user);

        $this->assertNotEmpty($user->getId());
        $this->assertSame(self::EMAIL, $user->getEmail());
        $this->assertNotSame(self::PASSWORD, $user->getPassword());
        $this->tester->seeInRepository(User::class, ['email' => self::EMAIL]);
    }
}
