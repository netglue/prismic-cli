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
use Psr\Container\ContainerInterface;

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
    private function kitchenSinkConfig(): array
    {
        $aggregator = new ConfigAggregator([
            ConfigProvider::class,
            ApiToolsConfigProvider::class,
            CustomTypeApiConfigProvider::class,
            new ArrayProvider($this->validConfig()),
        ]);

        return $aggregator->getMergedConfig();
    }

    /** @return array<string, mixed> */
    private function generalPlusApiConfig(): array
    {
        $aggregator = new ConfigAggregator([
            ConfigProvider::class,
            ApiToolsConfigProvider::class,
            new ArrayProvider($this->validConfig()),
        ]);

        return $aggregator->getMergedConfig();
    }

    /** @return array<string, mixed> */
    private function buildOnlyConfig(): array
    {
        $aggregator = new ConfigAggregator([
            ConfigProvider::class,
            new ArrayProvider($this->validConfig()),
        ]);

        return $aggregator->getMergedConfig();
    }

    /** @param array<string, mixed> $config */
    private function serviceManager(array $config): ContainerInterface
    {
        $dependencies = $config['dependencies'];
        $dependencies['services']['config'] = $config;

        return new ServiceManager($dependencies);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return Generator<class-string, array{0: class-string, 1: ServiceManager}>
     */
    private function factoryGenerator(array $config): Generator
    {
        $container = $this->serviceManager($config);
        self::assertTrue($container->has('config'));
        $config = $container->get('config');
        $factories = $config['dependencies']['factories'] ?? null;
        self::assertIsArray($factories);
        $services = array_keys($factories);

        foreach ($services as $serviceId) {
            yield $serviceId => [$serviceId, $container];
        }
    }

    /** @return Generator<class-string, array{0: class-string, 1: ServiceManager}> */
    public function kitchenSinkDataProvider(): Generator
    {
        return $this->factoryGenerator($this->kitchenSinkConfig());
    }

    /** @dataProvider kitchenSinkDataProvider */
    public function testThatConfigProvidersCanProduceAllRequiredDependenciesGivenValidConfig(string $serviceId, ContainerInterface $container): void
    {
        self::assertTrue($container->has($serviceId));
        self::assertIsObject($container->get($serviceId));
    }

    /** @return Generator<class-string, array{0: class-string, 1: ServiceManager}> */
    public function generalUsageDataProvider(): Generator
    {
        return $this->factoryGenerator($this->generalPlusApiConfig());
    }

    /** @dataProvider generalUsageDataProvider */
    public function testGeneralUsage(string $serviceId, ContainerInterface $container): void
    {
        self::assertTrue($container->has($serviceId));
        self::assertIsObject($container->get($serviceId));
    }

    /** @return Generator<class-string, array{0: class-string, 1: ServiceManager}> */
    public function buildOnlyDataProvider(): Generator
    {
        return $this->factoryGenerator($this->buildOnlyConfig());
    }

    /** @dataProvider buildOnlyDataProvider */
    public function testBuildOnlyUsage(string $serviceId, ContainerInterface $container): void
    {
        self::assertTrue($container->has($serviceId));
        self::assertIsObject($container->get($serviceId));
    }
}
