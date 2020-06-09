<?php
declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

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
