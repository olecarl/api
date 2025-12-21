<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Support\FunctionalTester;

final class ApiEntryCest
{
    public function _before(FunctionalTester $I): void
    {
        // $I->haveHttpHeader('Accept', 'text/html');
    }

    public function testApiEntry(FunctionalTester $I): void
    {
        $I->amOnPage('/docs');
        $I->seeResponseCodeIsSuccessful();
    }
}
