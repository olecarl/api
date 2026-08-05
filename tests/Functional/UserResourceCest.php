<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use App\Tests\Support\FunctionalTester;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserResourceCest
{
    public function testAuthenticatedUserCanReadOwnProfileWithoutPassword(FunctionalTester $I): void
    {
        $user = $this->createUser($I, 'user@example.com', 'correct horse battery staple');
        $I->amLoggedInAs($user, 'api');
        $I->sendGet('/v1/users/'.$user->getId());

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true, 512, \JSON_THROW_ON_ERROR);

        $I->assertSame('user@example.com', $response['email']);
        $I->assertSame(['ROLE_USER'], $response['roles']);
        $I->assertArrayNotHasKey('password', $response);
    }

    public function testAuthenticatedUserCannotReadAnotherProfile(FunctionalTester $I): void
    {
        $user = $this->createUser($I, 'user@example.com', 'correct horse battery staple');
        $otherUser = $this->createUser($I, 'other@example.com', 'correct horse battery staple');
        $I->amLoggedInAs($user, 'api');
        $I->sendGet('/v1/users/'.$otherUser->getId());

        $I->seeResponseCodeIs(403);
    }

    public function testUserCollectionRequiresAdministratorRole(FunctionalTester $I): void
    {
        $user = $this->createUser($I, 'user@example.com', 'correct horse battery staple');
        $I->amLoggedInAs($user, 'api');
        $I->sendGet('/v1/users');

        $I->seeResponseCodeIs(403);
    }

    private function createUser(FunctionalTester $I, string $email, string $password): User
    {
        $user = (new User())->setEmail($email);
        $user->setPassword($I->grabService(UserPasswordHasherInterface::class)->hashPassword($user, $password));

        $entityManager = $I->grabService(EntityManagerInterface::class);
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
