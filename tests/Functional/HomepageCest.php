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

final class HomepageCest
{
    public function _before(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Accept', 'text/html');
    }

    public function tryToAccessHomepage(FunctionalTester $I): void
    {
        $I->am('Visitor');
        $I->amGoingTo('access project homepage');
        $I->amOnPage('/');
        $I->expect('valid html response');
        $I->seeResponseCodeIsSuccessful();
        $I->haveHttpHeader('Content-Type', 'text/html');
    }
}
