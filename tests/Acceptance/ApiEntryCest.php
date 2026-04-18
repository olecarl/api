<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

final class ApiEntryCest
{
    public function _before(AcceptanceTester $I): void
    {
    }

    // All `public` methods will be executed as tests.
    public function tryToTest(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->seeResponseCodeIsSuccessful();
    }
}
