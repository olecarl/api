<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Support\FunctionalTester;

final class ApiDocsCest
{
    public function _before(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Accept', 'text/html');
    }

    public function tryToAccessHomepage(FunctionalTester $I): void
    {
        $I->am('website user');
        $I->amGoingTo('access project homepage');
        $I->amOnPage('/');
        $I->expect('valid html response');
        $I->seeResponseCodeIsSuccessful();
        $I->haveHttpHeader('Content-Type', 'text/html');
    }

    public function tryToAccessApiDocs(FunctionalTester $I): void
    {
        $I->am('website user');
        $I->amGoingTo('access api docs');
        $I->amOnPage('/docs');
        $I->expect('valid html response');
        $I->seeResponseCodeIsSuccessful();
        $I->haveHttpHeader('Content-Type', 'text/html');
    }
}
