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

namespace App\Tests\Support;

use Codeception\Actor;

/**
 * Inherited Methods.
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends Actor
{
    use _generated\FunctionalTesterActions;

    public function registerUser(string $email, string $password, bool $followRedirects): void
    {
        $this->amOnPage('/register');
        if (!$followRedirects) {
            $this->stopFollowingRedirects();
        }
        $this->submitSymfonyForm('registration_form', [
            '[email]' => $email,
            '[plainPassword]' => $password,
            '[agreeTerms]' => true,
        ]);
    }
}
