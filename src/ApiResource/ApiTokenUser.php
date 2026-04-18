<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\State\Processor\UserPasswordHasher;

#[ApiResource(
    shortName: 'ApiUser',
    operations: [
        new Get(),
        new GetCollection(normalizationContext: ['groups' => ['user:read']]),
        new Post(
            // denormalizationContext: ['groups' => ['user:create']],
            processor: UserPasswordHasher::class),
    ],
)]
class ApiTokenUser
{
}
