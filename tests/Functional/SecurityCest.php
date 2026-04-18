<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Support\FunctionalTester;

final class SecurityCest
{
    public function _before(FunctionalTester $I): void
    {
    }

    public function test(FunctionalTester $I): void
    {
        $I->am('Visitor');
        $I->amGoingTo('register a new user');
        $I->markTestSkipped();
    }
}
