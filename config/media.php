<?php

return [
    'image' => [
        'driver' => env('MEDIA_IMAGE_DRIVER', 'gd'),
        'max_width' => env('MEDIA_IMAGE_MAX_WIDTH', 1600),
        'max_height' => env('MEDIA_IMAGE_MAX_HEIGHT', 1600),
        'quality' => env('MEDIA_IMAGE_QUALITY', 70),
    ],
];

