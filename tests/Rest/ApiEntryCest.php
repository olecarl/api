<?php

declare(strict_types=1);

namespace App\Tests\Rest;

use App\Tests\Support\RestTester;

final class ApiEntryCest
{
    public function _before(RestTester $I): void
    {
    }

    public function testApiEntrypoint(RestTester $I): void
    {
        $I->sendGet('/docs');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
    }
}
