<?php

declare(strict_types=1);

namespace Primo\Cli;

use function array_filter;
use function implode;
use function sort;

final class TypeBuilder
{
    public const TYPE_UID = 'UID';
    public const TYPE_TEXT = 'Text';
    public const TYPE_BOOLEAN = 'Boolean';
    public const TYPE_COLOR = 'Color';
    public const TYPE_LINK = 'Link';
    public const TYPE_SELECT = 'Select';
    public const TYPE_DATE = 'Date';
    public const TYPE_TIMESTAMP = 'Timestamp';
    public const TYPE_NUMBER = 'Number';
    public const TYPE_GEOPOINT = 'GeoPoint';
    public const TYPE_EMBED = 'Embed';
    public const TYPE_GROUP = 'Group';
    public const TYPE_IMAGE = 'Image';
    public const TYPE_RANGE = 'Range';
    public const TYPE_RICH = 'StructuredText';
    public const TYPE_SLICE = 'Slice';
    public const TYPE_SLICE_ZONE = 'Slices';

    public const P     = 'paragraph';
    public const H1    = 'heading1';
    public const H2    = 'heading2';
    public const H3    = 'heading3';
    public const H4    = 'heading4';
    public const H5    = 'heading5';
    public const H6    = 'heading6';
    public const B     = 'strong';
    public const I     = 'em';
    public const A     = 'hyperlink';
    public const IMG   = 'image';
    public const PRE   = 'preformatted';
    public const UL    = 'list-item';
    public const OL    = 'o-list-item';
    public const RTL   = 'rtl';
    public const EMBED = 'embed';

    public const ALL = [
        self::P,
        self::H1,
        self::H2,
        self::H3,
        self::H4,
        self::H5,
        self::H6,
        self::B,
        self::I,
        self::A,
        self::IMG,
        self::PRE,
        self::UL,
        self::OL,
        self::RTL,
        self::EMBED,
    ];

    public const SLICE_DISPLAY_LIST = 'list';
    public const SLICE_DISPLAY_GRID = 'grid';

    private const SLICE_DISPLAY = [
        self::SLICE_DISPLAY_GRID,
        self::SLICE_DISPLAY_LIST,
    ];

    /** @return array<string, mixed> */
    public static function uid(string $label, string|null $placeholder = null): array
    {
        return [
            'type' => self::TYPE_UID,
            'config' => self::config($label, $placeholder),
        ];
    }

    /** @return array<string, mixed> */
    public static function text(string $label, string|null $placeholder = null, bool $useAsTitle = false): array
    {
        return [
            'type' => self::TYPE_TEXT,
            'config' => self::config($label, $placeholder, $useAsTitle),
        ];
    }

    /** @return array<string, mixed> */
    public static function boolean(string $label, string $falseLabel, string $trueLabel, bool $default): array
    {
        return [
            'type' => self::TYPE_BOOLEAN,
            'config' => [
                'label' => $label,
                'placeholder_false' => $falseLabel,
                'placeholder_true' => $trueLabel,
                'default_value' => $default,
            ],
        ];
    }

    /** @return array<string, mixed> */
    public static function color(string $label): array
    {
        return [
            'type' => self::TYPE_COLOR,
            'config' => ['label' => $label],
        ];
    }

    /** @return array<string, mixed> */
    public static function documentLink(
        string $label,
        string|null $placeholder = null,
        array|null $customTypes = null,
        array|null $tags = null,
    ): array {
        $config = array_filter([
            'select' => 'document',
            'label' => $label,
            'placeholder' => $placeholder,
            'customtypes' => $customTypes,
            'tags' => $tags,
        ]);

        return [
            'type' => self::TYPE_LINK,
            'config' => $config,
        ];
    }

    /** @return array<string, mixed> */
    public static function link(
        string $label,
        string|null $placeholder = null,
        bool $allowTargetBlank = false,
        array|null $customTypes = null,
    ): array {
        $config = array_filter([
            'label' => $label,
            'placeholder' => $placeholder,
            'allowTargetBlank' => $allowTargetBlank ? true : null,
            'customtypes' => empty($customTypes) ? null : $customTypes,
        ]);

        $config['select'] = null;

        return [
            'type' => self::TYPE_LINK,
            'config' => $config,
        ];
    }

    /** @return array<string, mixed> */
    public static function webLink(
        string $label,
        string|null $placeholder = null,
        bool $allowTargetBlank = false,
    ): array {
        return self::externalLink('web', $label, $placeholder, $allowTargetBlank);
    }

    /** @return array<string, mixed> */
    public static function mediaLink(
        string $label,
        string|null $placeholder = null,
        bool $allowTargetBlank = false,
    ): array {
        return self::externalLink('media', $label, $placeholder, $allowTargetBlank);
    }

    /** @return array<string, mixed> */
    private static function externalLink(
        string $type,
        string $label,
        string|null $placeholder = null,
        bool $allowTargetBlank = false,
    ): array {
        return [
            'type' => self::TYPE_LINK,
            'config' => array_filter([
                'select' => $type,
                'label' => $label,
                'placeholder' => $placeholder,
                'allowTargetBlank' => $allowTargetBlank ? true : null,
            ]),
        ];
    }

    /** @return array<string, mixed> */
    private static function config(string|null $label, string|null $placeholder = null, bool $useAsTitle = false): array
    {
        return array_filter([
            'label' => self::nullifyString($label),
            'placeholder' => self::nullifyString($placeholder),
            'useAsTitle' => $useAsTitle ? true : null,
        ]);
    }

    private static function nullifyString(string|null $string): string|null
    {
        return empty($string) ? null : $string;
    }

    /**
     * @param array<int, string> $options
     *
     * @return array<string, mixed>
     */
    public static function select(
        string $label,
        string|null $placeholder,
        array $options,
        string|null $default = null,
    ): array {
        return [
            'type' => self::TYPE_SELECT,
            'config' => array_filter([
                'label' => self::nullifyString($label),
                'placeholder' => self::nullifyString($placeholder),
                'options' => $options,
                'default_value' => $default,
            ]),
        ];
    }

    /** @return array<string, mixed> */
    public static function date(string $label, string|null $placeholder = null, bool $isToday = false): array
    {
        return self::datetimeType(self::TYPE_DATE, $label, $placeholder, $isToday);
    }

    /** @return array<string, mixed> */
    public static function timestamp(string $label, string|null $placeholder = null, bool $isToday = false): array
    {
        return self::datetimeType(self::TYPE_TIMESTAMP, $label, $placeholder, $isToday);
    }

    /** @return array<string, mixed> */
    private static function datetimeType(
        string $type,
        string $label,
        string|null $placeholder = null,
        bool $isToday = false,
    ): array {
        return [
            'type' => $type,
            'config' => array_filter([
                'label' => self::nullifyString($label),
                'placeholder' => self::nullifyString($placeholder),
                'default' => $isToday ? 'now' : null,
            ]),
        ];
    }

    /** @return array<string, mixed> */
    public static function number(
        string $label,
        string|null $placeholder = null,
        int|null $min = null,
        int|null $max = null,
    ): array {
        return [
            'type' => self::TYPE_NUMBER,
            'config' => array_filter([
                'label' => self::nullifyString($label),
                'placeholder' => self::nullifyString($placeholder),
                'min' => $min,
                'max' => $max,
            ], [self::class, 'filterNull']),
        ];
    }

    /** @return array<string, mixed> */
    public static function geoPoint(string $label): array
    {
        return [
            'type' => self::TYPE_GEOPOINT,
            'config' => ['label' => $label],
        ];
    }

    /** @return array<string, mixed> */
    public static function embed(string $label, string|null $placeholder = null): array
    {
        return [
            'type' => self::TYPE_EMBED,
            'config' => array_filter([
                'label' => self::nullifyString($label),
                'placeholder' => self::nullifyString($placeholder),
            ]),
        ];
    }

    /**
     * @param array<array-key, mixed> $fields
     *
     * @return array<string, mixed>
     */
    public static function group(string $label, array $fields, bool $repeatable = true): array
    {
        return [
            'type' => self::TYPE_GROUP,
            'config' => [
                'label' => $label,
                'repeat' => $repeatable,
                'fields' => $fields,
            ],
        ];
    }

    /** @return array<string, mixed> */
    public static function img(
        string $label,
        string|null $placeholder = null,
        int|null $x = null,
        int|null $y = null,
        array|null $views = null,
    ): array {
        return [
            'type' => self::TYPE_IMAGE,
            'config' => array_filter([
                'label' => $label,
                'placeholder' => $placeholder,
                'constraint' => [
                    'width' => $x,
                    'height' => $y,
                ],
                'thumbnails' => $views,
            ]),
        ];
    }

    /** @return array<string, mixed> */
    public static function imgView(string $name, int|null $x = null, int|null $y = null): array
    {
        return [
            'name' => $name,
            'width' => $x,
            'height' => $y,
        ];
    }

    /** @param array<array-key, string> $types */
    public static function textAllow(array $types): string
    {
        sort($types);

        return implode(',', $types);
    }

    public static function textAllowAll(): string
    {
        return self::textAllow(self::ALL);
    }

    /** @return array<array-key, string> */
    public static function blocksAll(): array
    {
        return self::ALL;
    }

    /** @return array<array-key, string> */
    public static function blocksText(): array
    {
        return [
            self::P,
            self::B,
            self::A,
            self::I,
            self::OL,
            self::UL,
            self::PRE,
            self::RTL,
            self::H1,
            self::H2,
            self::H3,
            self::H4,
            self::H5,
            self::H6,
        ];
    }

    /**
     * @param array<array-key, string> $allow
     * @param array<array-key, string> $labels
     *
     * @return array<string, mixed>
     */
    public static function richText(
        string $label,
        string|null $placeholder = null,
        array $allow = [],
        bool $multiple = true,
        bool $allowTargetBlank = false,
        bool $isTitle = false,
        array $labels = [],
        int|null $imgX = null,
        int|null $imgY = null,
    ): array {
        $t = $multiple ? 'multi' : 'single';
        $allowString = $allow === [] ? self::textAllowAll() : self::textAllow($allow);
        $config = [
            'label' => $label,
            'placeholder' => $placeholder,
            $t => $allowString,
            'allowTargetBlank' => $allowTargetBlank,
            'useAsTitle' => $isTitle,
            'labels' => $labels === [] ? null : $labels,
            'imageConstraint' => $imgX !== null || $imgY !== null
                ? array_filter(['width' => $imgX, 'height' => $imgY], [self::class, 'filterNull'])
                : null,
        ];

        return [
            'type' => self::TYPE_RICH,
            'config' => array_filter($config),
        ];
    }

    /**
     * @param array<string, mixed> $nonRepeatFields
     * @param array<string, mixed> $repeatFields
     *
     * @return array<string, mixed>
     */
    public static function slice(
        string $label,
        string|null $description = null,
        array $nonRepeatFields = [],
        array $repeatFields = [],
        string|null $icon = null,
        string|null $displayFormat = null,
    ): array {
        Assert::nullOrInArray($displayFormat, self::SLICE_DISPLAY);

        return array_filter([
            'type' => self::TYPE_SLICE,
            'fieldset' => $label,
            'description' => $description,
            'icon' => $icon,
            'display' => $displayFormat,
            'non-repeat' => $nonRepeatFields === [] ? null : $nonRepeatFields,
            'repeat' => $repeatFields === [] ? null : $repeatFields,
        ]);
    }

    /** @return array{name: string, display: string} */
    public static function sliceLabel(string $robots, string $humans): array
    {
        return [
            'name' => $robots,
            'display' => $humans,
        ];
    }

    /**
     * @param array<string, mixed> $slices
     * @param array<array-key, array{name: string, display: string}> $sliceLabels
     *
     * @return array<string, mixed>
     */
    public static function sliceZone(string $label, array $slices, array $sliceLabels = []): array
    {
        return [
            'type' => self::TYPE_SLICE_ZONE,
            'fieldset' => $label,
            'config' => array_filter([
                'labels' => $sliceLabels === [] ? null : $sliceLabels,
                'choices' => $slices,
            ]),
        ];
    }

    /** @psalm-assert !null $value */
    private static function filterNull(mixed $value): bool
    {
        return $value !== null;
    }

    /**
     * @param non-empty-string $label
     * @param int<0, max>      $min
     * @param int<1, max>      $max
     * @param int<1, max>      $step
     *
     * @psalm-return array{
     *   type: self::TYPE_RANGE,
     *   config: array{
     *     label: string,
     *     placeholder: string|null,
     *     min: int<0, max>,
     *     max: int<1, max>,
     *     step: int<1, max>,
     *   }
     * }
     */
    public static function range(string $label, string|null $placeholder, int $min, int $max, int $step): array
    {
        Assert::lessThan($min, $max);
        Assert::greaterThan($step, 0);
        Assert::greaterThan($min, 0);
        Assert::greaterThan($max, 1);

        return [
            'type' => self::TYPE_RANGE,
            'config' => [
                'label' => $label,
                'placeholder' => $placeholder,
                'min' => $min,
                'max' => $max,
                'step' => $step,
            ],
        ];
    }
}
