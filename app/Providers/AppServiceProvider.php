<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AI\AIServiceInterface;
use App\Services\AI\GeminiService;
use App\Services\AI\ChatServiceInterface;
use App\Services\AI\GroqService;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(AIServiceInterface::class, function ($app) {
            return new GeminiService();
        });

        $this->app->bind(ChatServiceInterface::class, function ($app) {
            return new GroqService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
