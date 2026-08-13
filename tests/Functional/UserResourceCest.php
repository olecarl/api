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
        $I->sendGet('/users/'.$user->getId());

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true, 512, \JSON_THROW_ON_ERROR);

        $I->assertSame('user@example.com', $response['email']);
        $I->assertSame((string) $user->getId(), $response['id']);
        $I->assertSame(['ROLE_USER'], $response['roles']);
        $I->assertArrayNotHasKey('password', $response);
    }

    public function testAuthenticatedUserCannotReadAnotherProfile(FunctionalTester $I): void
    {
        $user = $this->createUser($I, 'user@example.com', 'correct horse battery staple');
        $otherUser = $this->createUser($I, 'other@example.com', 'correct horse battery staple');
        $I->amLoggedInAs($user, 'api');
        $I->sendGet('/users/'.$otherUser->getId());

        $I->seeResponseCodeIs(403);
    }

    public function testAdministratorCanReadAnotherProfile(FunctionalTester $I): void
    {
        $admin = $this->createUser($I, 'admin@example.com', 'correct horse battery staple', ['ROLE_ADMIN']);
        $otherUser = $this->createUser($I, 'other@example.com', 'correct horse battery staple');
        $I->amLoggedInAs($admin, 'api');
        $I->sendGet('/users/'.$otherUser->getId());

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['id' => (string) $otherUser->getId(), 'email' => 'other@example.com']);
    }

    public function testUserCollectionRequiresAdministratorRole(FunctionalTester $I): void
    {
        $user = $this->createUser($I, 'user@example.com', 'correct horse battery staple');
        $I->amLoggedInAs($user, 'api');
        $I->sendGet('/users');

        $I->seeResponseCodeIs(403);
    }

    public function testAdministratorCanReadPaginatedUserCollection(FunctionalTester $I): void
    {
        $admin = $this->createUser($I, 'admin@example.com', 'correct horse battery staple', ['ROLE_ADMIN']);
        $this->createUser($I, 'first@example.com', 'correct horse battery staple');
        $this->createUser($I, 'second@example.com', 'correct horse battery staple');
        $I->amLoggedInAs($admin, 'api');

        $I->sendGet('/users?items=1');

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true, 512, \JSON_THROW_ON_ERROR);

        $I->assertCount(1, $response['member']);
        $I->assertSame(3, $response['totalItems']);
    }

    public function testAnonymousUserCannotReadUserCollection(FunctionalTester $I): void
    {
        $I->sendGet('/users');

        $I->seeResponseCodeIs(401);
    }

    public function testVersionedUserCollectionPathIsNotAvailable(FunctionalTester $I): void
    {
        $I->sendGet('/v1/users');

        $I->seeResponseCodeIs(404);
    }

    public function testMissingUserCannotBeRead(FunctionalTester $I): void
    {
        $user = $this->createUser($I, 'user@example.com', 'correct horse battery staple');
        $I->amLoggedInAs($user, 'api');
        $I->sendGet('/users/00000000-0000-4000-8000-000000000000');

        $I->seeResponseCodeIs(404);
    }

    public function testInvalidUserIdentifierCannotBeRead(FunctionalTester $I): void
    {
        $user = $this->createUser($I, 'user@example.com', 'correct horse battery staple');
        $I->amLoggedInAs($user, 'api');
        $I->sendGet('/users/not-a-uuid');

        $I->seeResponseCodeIs(404);
    }

    /**
     * @param list<string> $roles
     */
    private function createUser(FunctionalTester $I, string $email, string $password, array $roles = []): User
    {
        $user = (new User())->setEmail($email)->setRoles($roles);
        $user->setPassword($I->grabService(UserPasswordHasherInterface::class)->hashPassword($user, $password));

        $entityManager = $I->grabService(EntityManagerInterface::class);
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
