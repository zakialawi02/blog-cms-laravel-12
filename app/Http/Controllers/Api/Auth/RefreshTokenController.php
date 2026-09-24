<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefreshTokenController extends Controller
{
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $current = $user->currentAccessToken();
            if ($current) {
                $current->delete();
            }
            $token = $user->createToken('authToken', TokenAbility::abilitiesForRole($user->role ?? 'user'))->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed',
                'token' => $token,
                'token_type' => 'Bearer',
                'abilities' => TokenAbility::abilitiesForRole($user->role ?? 'user'),
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token. (' . \App\Support\ErrorReporter::refString($e, 'RefreshTokenController::refresh') . ')',
            ], 500);
        }
    }
}
