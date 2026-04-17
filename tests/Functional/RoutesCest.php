<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
            ['uri' => '/',              'accept' => 'text/html', 'status' => 200],
            ['uri' => '/login',         'accept' => 'text/html', 'status' => 200],
            ['uri' => '/register',      'accept' => 'text/html', 'status' => 200],
            ['uri' => '/verify-email',  'accept' => 'text/html', 'status' => 200],
        ];
    }
}
