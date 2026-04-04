<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserRepositoryInterface
{
    public function save(UserInterface $user, bool $flush = false): void;

    /** @psalm-api */
    public function remove(UserInterface $user, bool $flush = false): void;

    public function upgradePassword(UserInterface $user, string $newHashedPassword): void;
}
