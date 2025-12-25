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

final class ApiDocsCest
{
    public function _before(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Accept', 'text/html');
    }

    public function tryToAccessApiDocs(FunctionalTester $I): void
    {
        $I->am('website user');
        $I->amGoingTo('access api docs');
        $I->amOnPage('/docs');
        $I->expect('valid html response');
        $I->seeResponseCodeIsSuccessful();
        $I->haveHttpHeader('Content-Type', 'text/html');
        $I->expectTo('see the title');
        $I->see('Hello API Platform');
        // $I->seeLink('API Platform');
    }
}
