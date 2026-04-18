<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

final class ApiEntryPointCest
{
    public function _before(ApiTester $I): void
    {
    }

    public function tryToGetApiEntryPoint(ApiTester $I): void
    {
        $I->sendGET('/');
        $I->seeResponseCodeIsSuccessful();
    }
}
