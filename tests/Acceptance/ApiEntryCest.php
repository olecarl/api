<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

final class ApiEntryCest
{
    public function tryToAccessApiEntry(AcceptanceTester $I): void
    {
        $I->am('Visitor');
        $I->wantTo('access API entry');
        $I->amOnPage('/');
        $I->expectTo('see Response is successful');
        $I->seeResponseCodeIsSuccessful();
    }
}
