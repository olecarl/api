<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use App\Tests\Support\FunctionalTester;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserValidationCest
{
    public function testValidUserHasNoViolations(FunctionalTester $I): void
    {
        $user = (new User())
            ->setEmail('user@example.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword('hashed-password')
        ;

        $violations = $I->grabService(ValidatorInterface::class)->validate($user);

        $I->assertCount(0, $violations);
    }

    public function testUserRequiresValidEmailAndPassword(FunctionalTester $I): void
    {
        $user = (new User())
            ->setEmail('not-an-email')
        ;

        $violations = $I->grabService(ValidatorInterface::class)->validate($user);
        $messages = array_map(static fn ($violation): string => $violation->getMessage(), iterator_to_array($violations));

        $I->assertContains('Enter a valid email address.', $messages);
        $I->assertContains('Password is required.', $messages);
    }

    public function testUserRejectsInvalidRoleNames(FunctionalTester $I): void
    {
        $user = (new User())
            ->setEmail('user@example.com')
            ->setRoles(['administrator'])
            ->setPassword('hashed-password')
        ;

        $violations = $I->grabService(ValidatorInterface::class)->validate($user);

        $I->assertSame(1, $violations->count());
        $I->assertSame(
            'Roles must use the ROLE_ prefix and uppercase characters.',
            $violations->get(0)->getMessage(),
        );
    }

    public function testUserEmailMustBeUnique(FunctionalTester $I): void
    {
        $entityManager = $I->grabService(EntityManagerInterface::class);
        $existingUser = (new User())
            ->setEmail('user@example.com')
            ->setPassword('hashed-password')
        ;
        $entityManager->persist($existingUser);
        $entityManager->flush();

        $duplicateUser = (new User())
            ->setEmail('user@example.com')
            ->setPassword('hashed-password')
        ;
        $violations = $I->grabService(ValidatorInterface::class)->validate($duplicateUser);

        $I->assertSame(1, $violations->count());
        $I->assertSame('This email address is already registered.', $violations->get(0)->getMessage());
    }
}
