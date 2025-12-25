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

final class ApiEntryCest
{
    public function _before(RestTester $I): void
    {
    }

    public function testApiEntrypoint(RestTester $I): void
    {
        $I->am('API user');
        $I->amGoingTo('access api entrypoint');
        $I->sendGet('/docs');
        $I->expect('valid json response');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
    }
}
