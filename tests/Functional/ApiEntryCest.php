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
