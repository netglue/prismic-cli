<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Unit\Container;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Primo\Cli\Container\CustomTypeClientFactory;
use Primo\Cli\Exception\AssertionFailed;
use Prismic\DocumentType\Client;
use Psr\Container\ContainerInterface;

class CustomTypeClientFactoryTest extends TestCase
{
    /** @var MockObject&ContainerInterface */
    private $container;
    /** @var CustomTypeClientFactory */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory = new CustomTypeClientFactory();
    }

    public function testThatAnExceptionIsThrownWhenConfigurationDoesNotContainAToken(): void
    {
        $this->container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn([]);

        $this->expectException(AssertionFailed::class);
        $this->expectExceptionMessage('config.primo.custom-type-api.token');

        ($this->factory)($this->container);
    }

    public function testThatAnExceptionIsThrownWhenConfigurationDoesNotContainARepositoryName(): void
    {
        $this->container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn([
                'primo' => [
                    'custom-type-api' => [
                        'token' => 'Foo',
                    ],
                ],
            ]);

        $this->expectException(AssertionFailed::class);
        $this->expectExceptionMessage('config.primo.custom-type-api.repository');

        ($this->factory)($this->container);
    }

    public function testThatAClientCanBeReturnedWhenTheContainerDoesNotHaveAnyHttpComponents(): void
    {
        $this->container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn([
                'primo' => [
                    'custom-type-api' => [
                        'token' => 'Foo',
                        'repository' => 'Bar',
                    ],
                ],
            ]);

        $this->container->expects(self::exactly(4))
            ->method('has')
            ->willReturn(false);

        $client = ($this->factory)($this->container);
        self::assertInstanceOf(Client::class, $client);
    }
}
