<?php

return [
    'image' => [
        'driver' => env('MEDIA_IMAGE_DRIVER', 'gd'),
        // Оптимизированные размеры для веб-изображений
        'max_width' => env('MEDIA_IMAGE_MAX_WIDTH', 1200),
        'max_height' => env('MEDIA_IMAGE_MAX_HEIGHT', 1200),
        // Качество 60% - оптимальный баланс между качеством и размером файла
        'quality' => env('MEDIA_IMAGE_QUALITY', 60),
    ],
];

