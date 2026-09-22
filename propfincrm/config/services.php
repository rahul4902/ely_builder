<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'dinero' => [
        'secret' => env('DINERO_SECRET', 'SECRET'),
        'client' => env('DINERO_CLIENTID', 'CLIENTID'),
    ],

    'firebase' => [
        'credentials_path' => env('FIREBASE_CREDENTIALS_PATH', storage_path('app/firebase/service-account.json')),
        'project_id' => env('FIREBASE_PROJECT_ID'),
    ],

    'scheduler' => [
        'token' => env('SCHEDULER_TOKEN'),
    ],

    'razorpay' => [
        'key' => env('RAZORPAY_KEY_ID', 'rzp_test_YourKeyId'),
        'secret' => env('RAZORPAY_KEY_SECRET', 'YourKeySecret'),
    ],

];
