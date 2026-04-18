<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;

final class RoutesCest
{
    public function _before(FunctionalTester $I): void
    {
    }

    #[DataProvider('routesProvider')]
    public function tryToAccessDefaultRoutes(FunctionalTester $I, Example $example): void
    {
        $I->haveHttpHeader('Accept', $example['accept']);
        $I->am('Visitor');
        $I->amGoingTo('access default route');
        $I->amOnPage($example['uri']);
        $I->expect('matching response');
        $I->seeResponseCodeIs($example['status']);
        $I->assertResponseHasHeader('Content-Type', $example['accept']);
    }

    private function routesProvider(): array
    {
        return [
            ['uri' => '/', 'accept' => 'text/html', 'status' => 200],
        ];
    }
}
