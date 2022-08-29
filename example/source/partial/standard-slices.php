<?php

declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

/**
 * Typically, its a good idea to configure each slice in its own file, that way you can create slice collections from
 * a library - maybe you have a different set of slices for an article vs a regular web page for example.
 *
 * A big drawback to this approach is that slice variations are defined outside of slices in Prismic. As you can see,
 * the carousel below has a couple of 'variations' in the third arg to T::sliceZone().
 */

return T::sliceZone('Document Body', [
    'carousel' => T::slice('Banner Carousel', 'An image carousel with captions', [
        'auto-play' => T::boolean('Auto Play Slides?', 'No', 'Yes', false),
        'duration' => T::number('Delay between slides', '(In seconds)', 2, 30),
    ], [
        'image' => T::img('Slide Image', null, 1600, 400, [
            T::imgView('portrait', 400, 800),
            T::imgView('portrait-large', 800, 1600),
        ]),
        'title' => T::richText('Caption Title', 'Set a title for the caption', ['heading2'], false),
        'caption' => T::richText('Caption Text', 'Say something', ['paragraph', 'em', 'strong'], false),
    ]),
    'body-text' => T::richText('Standard Prose', null, T::blocksAll(), true, true),
], [
    'carousel' => [
        T::sliceLabel('dark-mode', 'Dark Styling'),
        T::sliceLabel('pink-mode', 'Make it Pink'),
    ],
]);
