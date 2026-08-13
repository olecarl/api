<?php

declare(strict_types=1);

namespace App\Tests\Unit\Configuration;

use App\Tests\Support\UnitTester;
use Symfony\Component\Yaml\Yaml;

final class ApiConfigurationCest
{
    public function testApiRoutesHaveNoVersionPrefix(UnitTester $I): void
    {
        $configuration = Yaml::parseFile(\dirname(__DIR__, 3).'/config/routes/api_platform.yaml');

        $I->assertArrayNotHasKey('prefix', $configuration['api_platform']);
        $I->assertArrayNotHasKey('requirements', $configuration['api_platform']);
        $I->assertArrayNotHasKey('defaults', $configuration['api_platform']);
    }

    public function testApiDocumentationIsDisabledInProduction(UnitTester $I): void
    {
        $configuration = Yaml::parseFile(\dirname(__DIR__, 3).'/config/packages/api_platform.yaml');
        $production = $configuration['when@prod']['api_platform'];

        $I->assertFalse($production['enable_swagger_ui']);
        $I->assertFalse($production['enable_swagger']);
        $I->assertFalse($production['enable_entrypoint']);
        $I->assertFalse($production['enable_docs']);
        $I->assertFalse($production['enable_profiler']);
    }
}
