<?php

namespace App\Providers;

use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Add captcha
        \Statamic\Tags\Tags::register('captcha', \App\Tags\Captcha::class);

        Validator::extend('recaptcha', function ($attribute, $value, $parameters, $validator) {
            $rule = new Recaptcha();

            // The new ValidationRule uses the fail callback pattern
            $failed = false;

            $rule->validate($attribute, $value, function () use (&$failed) {
                $failed = true;
            });

            return !$failed;
        });
    }
}
