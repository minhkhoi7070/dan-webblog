<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Video Upload Size (in Kilobytes)
    |--------------------------------------------------------------------------
    |
    | Maximum file size allowed for author video uploads in kilobytes.
    | Default is 100MB (102400 KB). Configurable via .env MAX_VIDEO_SIZE_KB.
    |
    */
    'max_video_size_kb' => (int) env('MAX_VIDEO_SIZE_KB', 102400),
];
