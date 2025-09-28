<?php

namespace App\Providers;

use Inertia\Inertia;
use Anhskohbo\NoCaptcha\NoCaptcha;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Tighten\Ziggy\Ziggy;

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
        Validator::extend('captcha', function ($attribute, $value, $parameters, $validator) {
            return app(NoCaptcha::class)->verifyResponse($value);
        });
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
        Inertia::share('csrf_token', csrf_token());
        $this->configureRateLimiting();
         Inertia::share([
        'ziggy' => fn () => (new Ziggy)->toArray(),
    ]);
    }
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('comments', function ($request) {
            return Limit::perMinute(3)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
