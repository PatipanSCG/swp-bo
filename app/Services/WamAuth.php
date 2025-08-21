<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WamAuth
{
    protected $email;
    protected $password;

    public function __construct()
    {
        $this->email = config('services.wam.email');
        $this->password = config('services.wam.password');
    }

    public function login(): ?string
    {
        $response = Http::withHeaders([
                'accept'       => 'application/json',
                'content-type' => 'application/json',
                'origin'       => 'https://wam.dit.go.th',
                'referer'      => 'https://wam.dit.go.th/login',
                'user-agent'   => 'Mozilla/5.0',
            ])
            ->withCookies([
                'CBWM-language' => 'en',
                'emailLogin'    => '',
                'passLogin'     => '',
                'firstLogin'    => '0',
            ], 'wam.dit.go.th')
            ->withOptions([
                'version' => CURL_HTTP_VERSION_1_1,
                'curl'    => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4],
            ])
            ->timeout(60)
            ->connectTimeout(10)
            ->post('https://wam.dit.go.th/api/v1/auth/login', [
                'email'    => $this->email,
                'password' => $this->password,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['accessToken'] ?? null;
        }

        return null;
    }
}
