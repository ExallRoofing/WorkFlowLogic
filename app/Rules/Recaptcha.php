<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.recaptcha.secret');

        // Support both field names:
        // - g_recaptcha_response     (your blueprint)
        // - g-recaptcha-response     (Google default)
        $token = $value ?: request('g-recaptcha-response');

        if (!$secret || !$token) {
            $fail('Captcha verification failed.');
            return;
        }

        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => $secret,
                'response' => $token,
            ]
        );

        if (!($response->json('success') === true)) {
            $fail('Captcha verification failed.');
        }
    }

}
