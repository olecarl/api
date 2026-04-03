<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\User;
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

    public function testValidUser(): void
    {
        $user = User::create(self::EMAIL, self::PASSWORD);
        $this->assertSame(User::ROLE_USER, $user->getRoles()[0]);
        $this->assertFalse($user->isVerified());
    }
}
