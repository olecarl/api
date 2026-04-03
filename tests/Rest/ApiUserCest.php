<?php

declare(strict_types=1);

namespace App\Tests\Rest;

use App\Tests\Factory\UserFactory;
use App\Tests\Support\RestTester;

final class ApiUserCest
{
    public function _before(RestTester $I): void
    {
        $I->haveHttpHeader('Accept', 'application/ld+json');
    }

    public function tryToGetUsers(RestTester $I): void
    {
        // UserFactory::createMany(10);
        $I->sendGET('/users');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}
