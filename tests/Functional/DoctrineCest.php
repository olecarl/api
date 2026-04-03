<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use App\Model\UserRepositoryInterface;
use App\Repository\UserRepository;
use App\Tests\Support\FunctionalTester;

final class DoctrineCest
{
    public function grabNumRecords(FunctionalTester $I): void
    {
        $I->haveInRepository(User::class, [
            'email' => 'ole@webconsole.de',
            'password' => 'XS2Test',
        ]);

        $numRecords = $I->grabNumRecords(User::class);
        $I->assertEquals(1, $numRecords);
    }

    public function grabRepository(FunctionalTester $I): void
    {
        $I->haveInRepository(User::class, [
            'email' => 'ole@webconsole.de',
            'password' => 'XS2Test',
        ]);

        $user = $I->grabEntityFromRepository(User::class, [
            'email' => 'ole@webconsole.de',
        ]);

        $repository = $I->grabRepository($user);
        $I->assertInstanceOf(UserRepository::class, $repository);

        $repository = $I->grabRepository(UserRepositoryInterface::class);
        $I->assertInstanceOf(UserRepository::class, $repository);
    }
}
