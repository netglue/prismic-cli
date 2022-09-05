<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Unit\Type;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Primo\Cli\Exception\PersistenceError;
use Primo\Cli\Type\RemotePersistence;
use Primo\Cli\Type\Spec;
use Prismic\DocumentType\Client;
use Prismic\DocumentType\Definition;
use Prismic\DocumentType\Exception\AuthenticationFailed;
use Prismic\DocumentType\Exception\DefinitionNotFound;

use function assert;
use function is_array;
use function reset;

class RemotePersistenceTest extends TestCase
{
    /** @var MockObject&Client */
    private Client $client;
    private RemotePersistence $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(Client::class);
        $this->storage = new RemotePersistence($this->client);
    }

    public function testThatHasReturnsTrueWhenTheTypeExists(): void
    {
        $this->client->expects(self::once())
            ->method('getDefinition')
            ->with('example')
            ->willReturn(Definition::new('foo', 'foo', true, true, 'foo'));

        self::assertTrue($this->storage->has('example'));
    }

    public function testThatHasReturnsFalseWhenTheTypeDoesNotExist(): void
    {
        /** @psalm-suppress InternalMethod */
        $this->client->expects(self::once())
            ->method('getDefinition')
            ->with('example')
            ->willThrowException(new DefinitionNotFound('Whut?', 0));

        self::assertFalse($this->storage->has('example'));
    }

    public function testThatHasThrowsPersistenceErrorWhenAnyOtherErrorOccurs(): void
    {
        /** @psalm-suppress InternalMethod */
        $this->client->expects(self::once())
            ->method('getDefinition')
            ->with('example')
            ->willThrowException(new AuthenticationFailed('Whut?', 0));

        $this->expectException(PersistenceError::class);
        $this->storage->has('example');
    }

    public function testReadThrowsPersistenceErrorWhenTypeIsNotFound(): void
    {
        /** @psalm-suppress InternalMethod */
        $this->client->expects(self::once())
            ->method('getDefinition')
            ->with('example')
            ->willThrowException(new DefinitionNotFound('Whut?', 0));

        $this->expectException(PersistenceError::class);
        $this->storage->read('example');
    }

    public function testThatReadReturnsTheDefinitionWhenTheTypeExists(): void
    {
        $definition = Definition::new('foo', 'foo', true, true, 'foo');
        $this->client->expects(self::once())
            ->method('getDefinition')
            ->with('example')
            ->willReturn($definition);

        self::assertSame($definition, $this->storage->read('example'));
    }

    public function testThatWriteThrowsPersistenceErrorForAnyFailure(): void
    {
        $definition = Definition::new('foo', 'foo', true, true, 'foo');
        /** @psalm-suppress InternalMethod */
        $this->client->expects(self::once())
            ->method('saveDefinition')
            ->with($definition)
            ->willThrowException(new AuthenticationFailed('Whut?', 0));

        $this->expectException(PersistenceError::class);
        $this->storage->write($definition);
    }

    public function testThatAllThrowsPersistenceErrorForAnyFailure(): void
    {
        /** @psalm-suppress InternalMethod */
        $this->client->expects(self::once())
            ->method('fetchAllDefinitions')
            ->willThrowException(new AuthenticationFailed('Whut?', 0));

        $this->expectException(PersistenceError::class);
        $this->storage->all();
    }

    public function testThatIndexSpecsWillReturnAnIterableWithTheExpectedSpec(): void
    {
        $definition = Definition::new('id', 'label', true, true, 'foo');
        $this->client->expects(self::once())
            ->method('fetchAllDefinitions')
            ->willReturn([$definition]);

        $result = $this->storage->indexSpecs();
        self::assertCount(1, $result);
        self::assertContainsOnlyInstancesOf(Spec::class, $result);
        assert(is_array($result));
        $spec = reset($result);
        self::assertEquals('id', $spec->id());
        self::assertEquals('label', $spec->name());
        self::assertTrue($spec->repeatable());
    }

    public function testThatDisabledTypesWillNotBeListedInTheIndex(): void
    {
        $definitions = [
            Definition::new('id', 'label', true, true, 'foo'),
            Definition::new('id2', 'label2', true, false, 'foo'),
        ];

        $this->client->expects(self::once())
            ->method('fetchAllDefinitions')
            ->willReturn($definitions);
        $result = $this->storage->indexSpecs();
        self::assertCount(1, $result);
        assert(is_array($result));
        $spec = reset($result);
        self::assertEquals('id', $spec->id());
    }

    public function testThatIndexThrowsPersistenceErrorForAnyFailure(): void
    {
        /** @psalm-suppress InternalMethod */
        $this->client->expects(self::once())
            ->method('fetchAllDefinitions')
            ->willThrowException(new AuthenticationFailed('Whut?', 0));

        $this->expectException(PersistenceError::class);
        $this->storage->indexSpecs();
    }
}
