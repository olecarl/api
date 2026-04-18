<?php

declare(strict_types=1);

namespace App\Tests\Rest;

use App\Tests\Support\RestTester;

final class ApiLoginCest
{
    public function _before(RestTester $I): void
    {
        $I->haveHttpHeader('Accept', 'application/json');
    }

    public function tryToLogin(RestTester $I): void
    {
        $I->markTestSkipped();
        /**
        $I->send('POST', '/auth', [
            'email' => 'ole@webconsole.de',
            'password' => 'XS2Test'
        ]);
        $I->seeResponseCodeIsSuccessful(); **/
    }
}
