<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'whatsapp' => [
        'provider' => env('WHATSAPP_PROVIDER', 'ultramsg'),

        'ultramsg_instance' => env('ULTRAMSG_INSTANCE_ID'),
        'ultramsg_token' => env('ULTRAMSG_TOKEN'),

        'twilio_sid' => env('TWILIO_SID'),
        'twilio_token' => env('TWILIO_TOKEN'),
        'twilio_from' => env('TWILIO_WHATSAPP_FROM'),

        'meta_phone_id' => env('META_WHATSAPP_PHONE_ID'),
        'meta_token' => env('META_WHATSAPP_TOKEN'),
    ],

];
