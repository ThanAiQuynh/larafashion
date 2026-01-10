<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | An HTTP or HTTPS URL to notify your application (a webhook) when
    | the process of uploads, deletes, and any API that accepts
    | notification_url has completed.
    |
    */
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Cloudinary settings. Cloudinary is a cloud
    | hosted media management service for all file uploads, storage,
    | delivery, and transformation needs.
    |
    |
    */
    'cloud_url' => env('CLOUDINARY_URL'),

    /**
     * Upload Preset From Cloudinary Dashboard
     *
     */
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    /**
     * Upload Route Customization
     *
     */
    'upload_route' => env('CLOUDINARY_UPLOAD_ROUTE', 'cloudinary/upload'),

    /**
     * File Types
     *
     */
    'file_types' => [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'ico', 'tiff', 'tif', 'svg', 'webp'],
        'video' => ['mp4', 'webm', 'ogg', 'avi', 'mov', 'flv', 'wmv', 'mkv', '3gp', '3g2'],
        'raw'   => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'txt', 'csv', 'json', 'xml'],
    ],

];
