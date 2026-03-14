<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class WelcomeController extends Controller
{
    /**
     * Display a welcome message for the API.
     */
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'message' => 'Welcome to Blog CMS API',
            'version' => 'v1',
            'endpoints' => [
                'main' => url('/api/v1'),
            ],
            'documentation' => url('/api/documentation'),
        ]);
    }
}
