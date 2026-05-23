<?php

return [
    'client_id' => env('DOKU_CLIENT_ID', ''),
    'secret_key' => env('DOKU_SECRET_KEY', ''),
    'base_url' => env('DOKU_BASE_URL', 'https://api-sandbox.doku.com'), // sandbox, ganti ke https://api.doku.com untuk production
];
