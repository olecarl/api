<?php

declare(strict_types=1);

namespace App\Tests\Rest;

use App\Tests\Support\RestTester;

final class ApiLoginCest
{
    public function _before(RestTester $I): void
    {
    }

    public function tryToLogin(RestTester $I): void
    {
        $I->markTestSkipped();
        /*
         * $I->sendPOST('/auth', [
         * 'email' => 'ole@webconsole.de',
         * 'password' => 'XS2Test'
         * ]);
         * $I->seeResponseCodeIsSuccessful(); **/
    }
}
