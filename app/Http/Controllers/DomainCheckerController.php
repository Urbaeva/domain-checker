<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DomainCheckerController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $domain = $request->input('domain');
        $apiUrl = "https://www.whoisxmlapi.com/whoisserver/WhoisService";
        $apiKey = env('WHOIS_API_KEY');

        $response = Http::get($apiUrl, [
            'apiKey' => $apiKey,
            'domainName' => $domain,
            'outputFormat' => 'JSON',
        ]);

        if (!$response->successful()) {
            return $this->errorResponse('Error fetching data');
        }

        $data = $response->json();

        if (!isset($data['WhoisRecord'])) {
            return $this->domainUnavailableResponse($domain);
        }

        $whois = $data['WhoisRecord'];
        $status = $this->extractStatus($whois);
        $expiryDate = $this->extractExpiryDate($whois);

        return response()->json([
            'domain' => $domain,
            'status' => $status,
            'expiry_date' => $expiryDate,
        ]);
    }

    private function extractStatus(array $whois): ?string
    {
        if (isset($whois['registryData']['header'])) {
            if (preg_match('/Domain\s+\S+\s+\((.*?)\)/', $whois['registryData']['header'], $matches)) {
                return $matches[1];
            }
        }

        return $whois['status'] ?? null;
    }

    private function extractExpiryDate(array $whois): ?string
    {
        return $whois['expiresDate'] ?? $whois['registryData']['expiresDate'] ?? null;
    }

    private function errorResponse(string $message): JsonResponse
    {
        return response()->json([
            'status' => $message,
            'expiry_date' => null,
        ], 500);
    }

    private function domainUnavailableResponse(string $domain): JsonResponse
    {
        return response()->json([
            'domain' => $domain,
            'status' => 'Unavailable',
            'expiry_date' => null,
        ]);
    }
}
