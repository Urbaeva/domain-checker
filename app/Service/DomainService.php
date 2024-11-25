<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class DomainService
{
    public function checkDomain($domain)
    {
        $response = Http::get('https://api.example.com/check', [
            'domain' => $domain,
            'apiKey' => 'api_key',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'is_available' => $data['available'] ?? null,
                'expiration_date' => $data['expiration_date'] ?? null,
            ];
        }

        return null;
    }
}
