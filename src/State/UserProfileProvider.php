<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\UserProfile as UserProfileResource;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\UserRepositoryInterface;

/**
 * @implements ProviderInterface<UserProfileResource>
 *
 * @psalm-api
 */
final readonly class UserProfileProvider implements ProviderInterface
{
    /**
     * @param UserRepositoryInterface<User> $userRepository
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserProvider $userProvider,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     *
     * @return UserProfileResource|iterable<UserProfileResource>|null
     */
    // @phpstan-ignore-next-line return.unusedType
    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->userRepository->findOneBy(['id' => $uriVariables['userId'] ?? null]);
        if (null === $user) {
            return null;
        }

        $profile = $user->getUserProfile();
        if (null === $profile) {
            return null;
        }

        return $this->toResource($profile);
    }

    private function toResource(UserProfile $profile): UserProfileResource
    {
        $user = $profile->getUser();
        if (null === $user) {
            throw new \LogicException('A user profile must belong to a user.');
        }

        $id = $profile->getId();
        if (null === $id) {
            throw new \LogicException('A user profile must have an identifier.');
        }

        return new UserProfileResource(
            $id,
            $this->userProvider->toResource($user),
            $profile->getAbout(),
        );
    }
}
