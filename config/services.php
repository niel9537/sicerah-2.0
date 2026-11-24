<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'bps' => [
        'api_key' => env('BPS_API_KEY', is_file(storage_path('app/private/bps-api-key'))
            ? trim(file_get_contents(storage_path('app/private/bps-api-key'))) : null),
        'domain' => env('BPS_DOMAIN', '1225'),
        'base_url' => 'https://webapi.bps.go.id/v1/api',
        'cache_store' => env('BPS_CACHE_STORE', 'file'),
        'ca_bundle' => env('BPS_CA_BUNDLE', is_file(storage_path('app/private/bps-ca-bundle.pem'))
            ? storage_path('app/private/bps-ca-bundle.pem') : null),
    ],

    'konvergensi' => [
        'cache_store' => env('KONVERGENSI_CACHE_STORE', 'file'),
        'ca_bundle' => env('KONVERGENSI_CA_BUNDLE', is_file(storage_path('app/private/bps-ca-bundle.pem'))
            ? storage_path('app/private/bps-ca-bundle.pem') : null),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
