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

namespace App\Tests\Rest;

use App\Tests\Support\RestTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;

final class ApiEntryCest
{
    public function _before(RestTester $I): void
    {
        $I->haveHttpHeader('Accept', 'application/ld+json');
    }

    public function testApiDocs(RestTester $I): void
    {
        $I->am('API user');
        $I->amGoingTo('access api docs');
        $I->sendGet('/docs');
        $I->expect('valid json response');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
    }

    #[DataProvider('formatProvider')]
    public function trySupportedFormats(RestTester $I, Example $example): void
    {
        $I->am('API_USER');
        $I->expect('content type is "'.$example['accept'].'"');
        $I->haveHttpHeader('accept', $example['accept']);
        $I->send('GET', '/');
        $I->seeResponseCodeIsSuccessful();
    }

    private function formatProvider(): array
    {
        return [
            ['accept' => 'application/ld+json'],
            ['accept' => 'application/json'],
        ];
    }
}
