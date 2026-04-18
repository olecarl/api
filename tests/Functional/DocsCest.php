<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Support\FunctionalTester;

final class DocsCest
{
    public function _before(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Accept', 'text/html');
    }

    public function tryToAccessDocs(FunctionalTester $I): void
    {
        $I->am('Visitor');
        $I->amGoingTo('access api docs');
        $I->amOnPage('/docs');
        $I->expect('valid html response');
        $I->seeResponseCodeIsSuccessful();
        $I->assertResponseHasHeader('Content-Type', 'text/html');
        $I->expectTo('be on route "api_doc"');
        $I->amOnRoute('api_doc');
        $I->expectTo('see "API Platform" in the response');
        $I->see('API Platform');
    }
}
