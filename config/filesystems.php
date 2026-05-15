<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => env('FILESYSTEM_DISK', 'local'),
            'root' => storage_path('app/public'),
            'url' => env('FILESYSTEM_DISK') === 'gcs' 
                ? "https://storage.googleapis.com/".env('GCS_BUCKET') 
                : env('APP_URL').'/storage',
            'project_id' => env('GCS_PROJECT_ID'),
            'bucket' => env('GCS_BUCKET'),
            'visibility' => 'public',
            'visibilityHandler' => \League\Flysystem\GoogleCloudStorage\UniformBucketLevelAccessVisibility::class,
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        'gcs' => [
            'driver' => 'gcs',
            'key_file_path' => env('GCS_KEY_FILE_PATH') ?: null, // will use ADC if null
            'project_id' => env('GCS_PROJECT_ID') ?: null, 
            'bucket' => env('GCS_BUCKET') ?: null,
            'path_prefix' => env('GCS_PATH_PREFIX', ''), 
            'url' => "https://storage.googleapis.com/".env('GCS_BUCKET'),
            'visibility' => null,
            'visibilityHandler' => \League\Flysystem\GoogleCloudStorage\UniformBucketLevelAccessVisibility::class,
            'storage_api_uri' => env('GCS_STORAGE_API_URI', null), 
            'metadata' => ['cacheControl' => 'public,max-age=86400'], 
            'throw' => true,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
