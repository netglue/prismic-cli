<?php

declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

return T::sharedSlice(
    'banner-header',
    'Banner Header Photo',
    'A full-width header photo with caption',
    [
        T::sharedSliceVariation(
            'default',
            'Default Style',
            '1',
            'The default banner style',
            [
                'heading' => T::richText('Main heading', null, [T::H1], false),
                'photo' => T::img('Image', null, 1600, 900, [
                    T::imgView('portrait', 600, 800),
                    T::imgView('square', 600, 600),
                ]),
                'text' => T::richText('Additional content', null, [T::P, T::A, T::B, T::I], true),
            ],
        ),
    ],
);
