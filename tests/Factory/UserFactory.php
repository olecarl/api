<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return User::class;
    }

    #[\Override]
    protected function defaults(): array
    {
        return [
            'createdAt' => self::faker()->dateTime(),
            'email' => self::faker()->text(180),
            'isVerified' => self::faker()->boolean(),
            'password' => self::faker()->text(),
            'roles' => [],
            'updatedAt' => self::faker()->dateTime(),
        ];
    }
}
