<?php

namespace Joshuapack\Cloudflare;

use Illuminate\Support\ServiceProvider;

class CloudflareServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/cloudflare.php' => config_path('cloudflare.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/cloudflare.php',
            'cloudflare'
        );

        $cloudflareConfig = config('cloudflare');

        $this->app->bind(Cloudflare::class, function () use ($cloudflareConfig) {
            $zone = $cloudflareConfig['zone'];
            if (!$zone || $zone == '') {
                $zone = null;
            }
            return new Cloudflare($cloudflareConfig['email'], $cloudflareConfig['key'], $cloudflareConfig['token'], $zone);
        });

        $this->app->alias(Cloudflare::class, 'laravel-cloudflare');
    }
}
