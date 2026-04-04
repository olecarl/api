<?php

declare(strict_types=1);

namespace App\Tests\Rest;

use App\Tests\Support\RestTester;

final class ApiUserCest
{
    public function _before(RestTester $I): void
    {
        $I->haveHttpHeader('Accept', 'application/ld+json');
    }

    public function tryToGetUsers(RestTester $I): void
    {
        $I->markTestSkipped();
    }

    public function tryToGetUser(RestTester $I): void
    {
        $I->markTestSkipped();
    }

    public function tryToPostUser(RestTester $I): void
    {
        $I->markTestSkipped();
    }

    public function tryToPutUser(RestTester $I): void
    {
        $I->markTestSkipped();
    }

    public function tryToPatchUser(RestTester $I): void
    {
        $I->markTestSkipped();
    }

    public function tryToDeleteUser(RestTester $I): void
    {
        $I->markTestSkipped();
    }
}
