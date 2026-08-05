<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\User as UserResource;
use App\Entity\User;
use App\Repository\UserRepository;

/**
 * @implements ProviderInterface<UserResource>
 *
 * @psalm-api
 */
final readonly class UserProvider implements ProviderInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     *
     * @return list<UserResource>|UserResource|null
     */
    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof GetCollection) {
            return array_map(
                $this->toResource(...),
                $this->userRepository->findAll(),
            );
        }

        $user = $this->userRepository->findOneBy(['id' => $uriVariables['id'] ?? null]);

        return null === $user ? null : $this->toResource($user);
    }

    private function toResource(User $user): UserResource
    {
        $id = $user->getId();
        if (null === $id) {
            throw new \LogicException('A user must have an identifier.');
        }

        return new UserResource(
            $id,
            $user->getEmail() ?? throw new \LogicException('A user must have an email address.'),
            array_values($user->getRoles()),
        );
    }
}
