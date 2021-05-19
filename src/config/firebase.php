<?php
return [
    'fcm_url' => env("FIREBASE_FCM_URL", 'https://fcm.googleapis.com/fcm/send'),
    'batch_url' => env("FIREBASE_BATCH_URL", 'https://iid.googleapis.com/iid/v1:batchAdd'),
    'api_key' => env("FIREBASE_API_KEY", '')
];
