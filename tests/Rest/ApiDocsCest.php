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

/** @noinspection PhpUnused */
final class ApiDocsCest
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
}
