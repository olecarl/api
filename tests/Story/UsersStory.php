<?php

declare(strict_types=1);

namespace App\Tests\Story;

use App\Tests\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UsersStory extends Story
{
    public function build(): void
    {
        // UserFactory::createMany(10);
    }
}
