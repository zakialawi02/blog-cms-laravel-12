<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');

        // Validate API Key
        if (!$apiKey || $apiKey !== config('app.api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing API Key',
                'hint' => 'Please include X-API-Key in your request headers',
                'example' => 'X-API-Key: your-api-key-here',
                'support' => 'Contact administrator for further assistance'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Skip domain validation if allow all domains is enabled
        if (config('cors.allow_all_origins', false)) {
            return $next($request);
        }

        // Detect domain automatically from Origin or Referer header
        $frontendDomain = $this->detectDomain($request);

        // Validate Frontend Domain using CORS allowed_origins
        $allowedDomains = $this->getAllowedOrigins();

        if (!$frontendDomain || !$this->isDomainAllowed($frontendDomain, $allowedDomains)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized domain',
                'hint' => 'Your domain is not in the allowed origins list',
                'your_domain' => $frontendDomain,
                'support' => 'Contact administrator for further assistance'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }

    /**
     * Detect frontend domain from request headers
     */
    private function detectDomain(Request $request): ?string
    {
        // Try Origin header first (most reliable for CORS requests)
        $origin = $request->header('Origin');
        if ($origin) {
            return $this->normalizeDomain($origin);
        }

        // Fallback to Referer header
        $referer = $request->header('Referer');
        if ($referer) {
            return $this->normalizeDomain($referer);
        }

        return null;
    }

    /**
     * Normalize domain URL to match allowed domains format
     * e.g., https://example.com/path -> https://example.com
     */
    private function normalizeDomain(string $url): string
    {
        $parsed = parse_url($url);
        
        if (!isset($parsed['scheme']) || !isset($parsed['host'])) {
            return $url;
        }

        $domain = $parsed['scheme'] . '://' . $parsed['host'];
        
        // Include port if present
        if (isset($parsed['port'])) {
            $domain .= ':' . $parsed['port'];
        }

        return $domain;
    }

    /**
     * Get allowed origins from CORS configuration
     */
    private function getAllowedOrigins(): array
    {
        $allowed = config('cors.allowed_origins', []);
        
        // Handle wildcard
        if (in_array('*', $allowed)) {
            return ['*'];
        }

        // Normalize all allowed origins
        return array_map(function ($origin) {
            return $this->normalizeDomain($origin);
        }, $allowed);
    }

    /**
     * Check if domain is allowed (supports patterns)
     */
    private function isDomainAllowed(string $domain, array $allowedDomains): bool
    {
        // Wildcard allows all
        if (in_array('*', $allowedDomains)) {
            return true;
        }

        // Check exact matches
        if (in_array($domain, $allowedDomains)) {
            return true;
        }

        // Check patterns (e.g., *.example.com)
        foreach ($allowedDomains as $allowed) {
            if (str_starts_with($allowed, '*.')) {
                $pattern = str_replace('*.', '', $allowed);
                if (str_ends_with($domain, '.' . $pattern) || $domain === $pattern) {
                    return true;
                }
            }
        }

        return false;
    }
}
