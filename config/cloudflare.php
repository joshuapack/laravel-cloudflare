<?php

return [
    /*
     * Your Cloudflare Email, used to login
     */
    'email' => env('CLOUDFLARE_EMAIL'),

    /*
     * Your Cloudflare API (Global) Key to pair with email
     */
    'key' => env('CLOUDFLARE_API_KEY'),

    /*
     * Your API Token, the preferable way
    'token' => env('CLOUDFLARE_TOKEN'),

    /*
     * The Zone you would like to modify
     */
    'zone' => env('CLOUDFLARE_ZONE_ID'),
];
