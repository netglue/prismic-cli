<?php

declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

/**
 * Some basics for a regular old web page.
 *
 * As this is just PHP, it's trivial to reuse slice definitions or other common configuration from other php files.
 */
return [
    'General' => [
        'title' => T::richText('Page Title', 'The title of your page', [T::H1], false, false, true),
        'description' => T::richText('Page Description', 'A description of this page for internal use', T::blocksText(), true),
    ],
    'Content' => [
        'body' => require __DIR__ . '/partial/standard-slices.php',
    ],
    'SEO' => require __DIR__ . '/partial/seo.php',
];
