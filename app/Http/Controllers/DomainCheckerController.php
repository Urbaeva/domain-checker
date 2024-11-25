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

        // Call the Whois API
        $response = Http::get($apiUrl, [
            'apiKey' => $apiKey,
            'domainName' => $domain,
            'outputFormat' => 'JSON',
        ]);

        // Return an error response if the API call fails
        if (!$response->successful()) {
            return $this->errorResponse('Error fetching data');
        }

        // Decode the API response
        $data = $response->json();

        // Check if Whois data exists
        if (!isset($data['WhoisRecord'])) {
            return $this->domainUnavailableResponse($domain);
        }

        // Extract relevant Whois information
        $whois = $data['WhoisRecord'];
        $status = $this->extractStatus($whois);
        $expiryDate = $this->extractExpiryDate($whois);

        // Return the domain information in the response
        return response()->json([
            'domain' => $domain,
            'status' => $status,
            'expiry_date' => $expiryDate,
        ]);
    }

    // Helper method to extract status from Whois data
    private function extractStatus(array $whois): ?string
    {
        if (isset($whois['registryData']['header'])) {
            if (preg_match('/Domain\s+\S+\s+\((.*?)\)/', $whois['registryData']['header'], $matches)) {
                return $matches[1];
            }
        }

        return $whois['status'] ?? null;
    }

    // Helper method to extract expiry date from Whois data
    private function extractExpiryDate(array $whois): ?string
    {
        return $whois['expiresDate'] ?? $whois['registryData']['expiresDate'] ?? null;
    }

    // Helper method to return the error response
    private function errorResponse(string $message): JsonResponse
    {
        return response()->json([
            'status' => $message,
            'expiry_date' => null,
        ], 500);
    }

    // Helper method to return domain unavailable response
    private function domainUnavailableResponse(string $domain): JsonResponse
    {
        return response()->json([
            'domain' => $domain,
            'status' => 'Unavailable',
            'expiry_date' => null,
        ]);
    }
}
