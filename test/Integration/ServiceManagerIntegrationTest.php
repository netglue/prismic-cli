<?php

declare(strict_types=1);

namespace Integration;

use Generator;
use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Primo\Cli\ApiToolsConfigProvider;
use Primo\Cli\ConfigProvider;
use Primo\Cli\CustomTypeApiConfigProvider;

use function array_keys;

final class ServiceManagerIntegrationTest extends TestCase
{
    /** @return array<string, mixed> */
    private function validConfig(): array
    {
        return [
            'prismic' => [
                'api' => 'https://your-repo.cdn.prismic.io/api/v2',
                'token' => null,
            ],
            'primo' => [
                'custom-type-api' => [
                    'token' => 'whatever',
                    'repository' => 'Something',
                ],
                'cli' => [
                    'builder' => [
                        'source' => __DIR__ . '/../Unit/build-specs/src',
                        'dist' => __DIR__ . '/../Unit/build-specs/dist',
                    ],
                ],
                'types' => [
                    [
                        'id' => 'example',
                        'name' => 'Example',
                        'repeatable' => true,
                    ],
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function mergedConfig(): array
    {
        $aggregator = new ConfigAggregator([
            ConfigProvider::class,
            ApiToolsConfigProvider::class,
            CustomTypeApiConfigProvider::class,
            new ArrayProvider($this->validConfig()),
        ]);

        return $aggregator->getMergedConfig();
    }

    private function serviceManager(): ServiceManager
    {
        $config = $this->mergedConfig();
        $dependencies = $config['dependencies'];
        $dependencies['services']['config'] = $config;

        return new ServiceManager($dependencies);
    }

    /** @return Generator<class-string, array{0: class-string, 1: ServiceManager}> */
    public function serviceDataProvider(): Generator
    {
        $container = $this->serviceManager();
        self::assertTrue($container->has('config'));
        $config = $container->get('config');
        $factories = $config['dependencies']['factories'] ?? null;
        self::assertIsArray($factories);
        $services = array_keys($factories);

        foreach ($services as $serviceId) {
            yield $serviceId => [$serviceId, $container];
        }
    }

    /** @dataProvider serviceDataProvider */
    public function testThatConfigProvidersCanProduceAllRequiredDependenciesGivenValidConfig(string $serviceId, ServiceManager $container): void
    {
        self::assertTrue($container->has($serviceId));
        self::assertIsObject($container->get($serviceId));
    }
}
