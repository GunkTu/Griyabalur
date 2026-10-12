<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->useConfiguredUrl();
    }

    /**
     * Behind a tunnel or reverse proxy the Host header can be anything (localhost, an internal IP).
     * In production every link and redirect is built from APP_URL instead, so the admin login and
     * the language links always stay on the public address.
     */
    public function useConfiguredUrl(): void
    {
        $url = config('app.url');

        if (! $url || $this->app->environment(['local', 'testing'])) {
            return;
        }

        URL::forceRootUrl($url);

        if (str_starts_with($url, 'https://')) {
            URL::forceScheme('https');
        }
    }
}
