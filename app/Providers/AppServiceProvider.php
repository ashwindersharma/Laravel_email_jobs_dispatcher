<?php

namespace App\Providers;

use App\Console\Commands\QueueWorkCommand;
use Illuminate\Queue\Console\WorkCommand;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        $this->app->singleton(WorkCommand::class, QueueWorkCommand::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        if ($this->app->runningInConsole()) {
            return;
        }

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
