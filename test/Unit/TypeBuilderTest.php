<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Unit;

use PHPUnit\Framework\TestCase;
use Primo\Cli\TypeBuilder as T;

class TypeBuilderTest extends TestCase
{
    public function testLinkConfigHasExpectedStructure(): void
    {
        $data = T::link('Label', 'Placeholder', false, null);
        $expect = [
            'type' => T::TYPE_LINK,
            'config' => [
                'select' => null,
                'label' => 'Label',
                'placeholder' => 'Placeholder',
            ],
        ];
        self::assertEquals($expect, $data);
    }

    public function testThatNumbersWillIncludeTheMinWhenZero(): void
    {
        $data = T::number('Number', 'Placeholder', 0, 100);
        $expect = [
            'type' => T::TYPE_NUMBER,
            'config' => [
                'label' => 'Number',
                'placeholder' => 'Placeholder',
                'min' => 0,
                'max' => 100,
            ],
        ];
        self::assertEquals($expect, $data);
    }

    /** @return array<array-key, array{0: int|null, 1: int|null, 2: array{width?:int, height?:int}}> */
    public function richTextImageConstraintProvider(): array
    {
        return [
            [999, null, ['width' => 999]],
            [null, 999, ['height' => 999]],
            [123, 456, ['width' => 123, 'height' => 456]],
        ];
    }

    /**
     * @param array{width?:int, height?:int} $expect
     *
     * @dataProvider richTextImageConstraintProvider
     */
    public function testWidthOrHeightWillYieldImageConstraintInRichText(?int $x, ?int $y, array $expect): void
    {
        $data = T::richText('Foo', 'Foo', [], true, true, true, [], $x, $y);
        self::assertArrayHasKey('config', $data);
        $config = $data['config'];
        self::assertIsArray($config);
        self::assertArrayHasKey('imageConstraint', $config);
        self::assertEquals($expect, $config['imageConstraint']);
    }

    public function testImageConstraintIsAbsentInRichTextWhenNeitherXNorYAreProvided(): void
    {
        $data = T::richText('Foo', null, []);
        self::assertArrayHasKey('config', $data);
        $config = $data['config'];
        self::assertIsArray($config);
        self::assertArrayNotHasKey('imageConstraint', $config);
    }
}
