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

    /** @return array<string, mixed> */
    public static function uid(string $label, ?string $placeholder = null): array
    {
        return [
            'type' => self::TYPE_UID,
            'config' => self::config($label, $placeholder),
        ];
    }

    /** @return array<string, mixed> */
    public static function text(string $label, ?string $placeholder = null, bool $useAsTitle = false): array
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
    public static function documentLink(string $label, ?string $placeholder = null, ?array $customTypes = null, ?array $tags = null): array
    {
        $config = array_filter([
            'select' => 'document',
            'label' => $label,
            'placeholder' => $placeholder,
        ]);

        if (! empty($customTypes)) {
            $config['customtypes'] = $customTypes;
        }

        if (! empty($tags)) {
            $config['tags'] = $tags;
        }

        return [
            'type' => self::TYPE_LINK,
            'config' => $config,
        ];
    }

    /** @return array<string, mixed> */
    public static function link(string $label, ?string $placeholder = null, bool $allowTargetBlank = false, ?array $customTypes = null): array
    {
        $config = array_filter([
            'label' => $label,
            'placeholder' => $placeholder,
            'allowTargetBlank' => $allowTargetBlank ? true : null,
            'customtypes' => empty($customTypes) ? null : $customTypes,
        ]);

        return [
            'type' => self::TYPE_LINK,
            'config' => $config,
        ];
    }

    /** @return array<string, mixed> */
    public static function webLink(string $label, ?string $placeholder = null, bool $allowTargetBlank = false): array
    {
        return self::externalLink('web', $label, $placeholder, $allowTargetBlank);
    }

    /** @return array<string, mixed> */
    public static function mediaLink(string $label, ?string $placeholder = null, bool $allowTargetBlank = false): array
    {
        return self::externalLink('media', $label, $placeholder, $allowTargetBlank);
    }

    /** @return array<string, mixed> */
    private static function externalLink(string $type, string $label, ?string $placeholder = null, bool $allowTargetBlank = false): array
    {
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
    private static function config(?string $label, ?string $placeholder = null, bool $useAsTitle = false): array
    {
        return array_filter([
            'label' => self::nullifyString($label),
            'placeholder' => self::nullifyString($placeholder),
            'useAsTitle' => $useAsTitle ? true : null,
        ]);
    }

    private static function nullifyString(?string $string): ?string
    {
        return empty($string) ? null : $string;
    }

    /**
     * @param array<int, string> $options
     *
     * @return array<string, mixed>
     */
    public static function select(string $label, ?string $placeholder, array $options, ?string $default = null): array
    {
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
    public static function date(string $label, ?string $placeholder = null, bool $isToday = false): array
    {
        return self::datetimeType(self::TYPE_DATE, $label, $placeholder, $isToday);
    }

    /** @return array<string, mixed> */
    public static function timestamp(string $label, ?string $placeholder = null, bool $isToday = false): array
    {
        return self::datetimeType(self::TYPE_TIMESTAMP, $label, $placeholder, $isToday);
    }

    /** @return array<string, mixed> */
    private static function datetimeType(string $type, string $label, ?string $placeholder = null, bool $isToday = false): array
    {
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
    public static function number(string $label, ?string $placeholder = null, ?int $min = null, ?int $max = null): array
    {
        return [
            'type' => self::TYPE_NUMBER,
            'config' => array_filter([
                'label' => self::nullifyString($label),
                'placeholder' => self::nullifyString($placeholder),
                'min' => $min,
                'max' => $max,
            ]),
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
    public static function embed(string $label, ?string $placeholder = null): array
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
    public static function img(string $label, ?string $placeholder = null, ?int $x = null, ?int $y = null, ?array $views = null): array
    {
        return [
            'type' => self::TYPE_IMAGE,
            'config' => array_filter([
                'label' => $label,
                'placeholder' => $placeholder,
                'constraint' => array_filter([
                    'width' => $x,
                    'height' => $y,
                ]),
                'thumbnails' => $views,
            ]),
        ];
    }

    /** @return array<string, mixed> */
    public static function imgView(string $name, ?int $x = null, ?int $y = null): array
    {
        return array_filter([
            'name' => $name,
            'width' => $x,
            'height' => $y,
        ]);
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
        ?string $placeholder = null,
        array $allow = [],
        bool $multiple = true,
        bool $allowTargetBlank = false,
        bool $isTitle = false,
        array $labels = [],
        ?int $imgX = null,
        ?int $imgY = null
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
            'imageConstraint' => $imgX && $imgY ? ['width' => $imgX, 'height' => $imgY] : null,
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
        ?string $description = null,
        array $nonRepeatFields = [],
        array $repeatFields = [],
        ?string $icon = null
    ): array {
        return array_filter([
            'type' => self::TYPE_SLICE,
            'fieldset' => $label,
            'description' => $description,
            'icon' => $icon,
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
}
