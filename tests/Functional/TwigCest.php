<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;

final class TwigCest
{
    public function _before(FunctionalTester $I): void
    {
    }

    #[DataProvider('routesProvider')]
    public function seeRenderedTemplate(FunctionalTester $I, Example $example): void
    {
        $I->amOnPage($example['uri']);
        $I->seeRenderedTemplate($example['template']);
        if (!empty($example['layout'])) {
            $I->seeRenderedTemplate($example['layout']);
        }
    }

    private function routesProvider(): array
    {
        return [
            ['uri' => '/', 'template' => 'default/index.html.twig'],
            ['uri' => '/login', 'template' => 'security/login.html.twig', 'layout' => 'base.html.twig'],
            ['uri' => '/register', 'template' => 'registration/register.html.twig', 'layout' => 'base.html.twig'],
        ];
    }
}
