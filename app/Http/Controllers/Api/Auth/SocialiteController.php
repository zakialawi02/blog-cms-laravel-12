<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function login(Request $request, string $provider)
    {
        $request->validate([
            'provider_token' => 'required|string',
        ]);

        if (!config("services.{$provider}")) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid provider',
            ], 400);
        }

        try {
            $providerToken = $request->provider_token;
            // Get user from provider
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver($provider);
            $socialUser = $driver->userFromToken($providerToken);

            // Generate username from provider or fallback to name/email
            $username = $this->generateUsername($socialUser, $provider);

            $user = User::updateOrCreate([
                'provider_id' => $socialUser->id,
                'provider_name' => $provider,
            ], [
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'email_verified_at' => now(),
                'username' => $username,
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken ?? null,
            ]);

            $token = $user->createToken('auth_token', TokenAbility::abilitiesForRole($user->role ?? 'user'))->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully with ' . ucfirst($provider),
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to login using ' . ucfirst($provider) . '. Invalid or expired token.',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    private function generateUsername($socialUser, $provider)
    {
        // Try to get username from provider
        $username = $socialUser->getNickname() ?? null;

        // If no username from provider, generate from name or email
        if (!$username) {
            if (!empty($socialUser->name)) {
                // Use name and convert to username format
                $username = strtolower(str_replace(' ', '_', $socialUser->name)) . '_' . rand(1000, 9999);
            } else {
                // Use email prefix as fallback
                $username = strtolower(explode('@', $socialUser->email)[0]) . '_' . rand(1000, 9999);
            }
        }

        // Clean username (remove special characters, keep only alphanumeric and underscore)
        $username = preg_replace('/[^a-z0-9_]/', '', strtolower($username));

        // Ensure username is unique
        $baseUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . '_' . $counter;
            $counter++;
        }

        return $username;
    }
}
