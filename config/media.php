<?php

return [
    'image' => [
        'driver' => env('MEDIA_IMAGE_DRIVER', 'gd'),
        'max_width' => env('MEDIA_IMAGE_MAX_WIDTH', 2560),
        'max_height' => env('MEDIA_IMAGE_MAX_HEIGHT', 2560),
        'quality' => env('MEDIA_IMAGE_QUALITY', 85),
    ],
];

