<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user, bool $flush = false): void;

    /** @psalm-api */
    public function remove(User $user, bool $flush = false): void;
}
