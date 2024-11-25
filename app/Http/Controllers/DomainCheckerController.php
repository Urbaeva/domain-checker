<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DomainCheckerController extends Controller
{
    public function check(Request $request)
    {
        $domain = $request->input('domain');

        $apiUrl = 'https://api.whoisapi.com/v1';
        $apiKey = env('WHOIS_API_KEY');

        $response = Http::get($apiUrl, [
            'apiKey' => $apiKey,
            'domainName' => $domain,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['domainAvailability']) && $data['domainAvailability'] === 'UNAVAILABLE') {
                return response()->json([
                    'domain' => $domain,
                    'status' => 'Unavailable',
                    'expiry_date' => $data['expiryDate'] ?? null,
                ]);
            }

            return response()->json([
                'domain' => $domain,
                'status' => 'Available',
                'expiry_date' => null,
            ]);
        }

        return response()->json([
            'domain' => $domain,
            'status' => 'Error fetching data',
            'expiry_date' => null,
        ], 500);
    }
}
