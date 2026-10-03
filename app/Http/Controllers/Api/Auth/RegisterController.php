<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Rules\RecaptchaRule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Handle the incoming registration request.
     */
    public function register(Request $request): JsonResponse
    {
        // Throttle: maksimal 5 percobaan registrasi per IP per 5 menit.
        $throttleKey = 'api-register|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many registration attempts. Please try again later.',
                'retry_after' => RateLimiter::availableIn($throttleKey),
            ], 429);
        }

        RateLimiter::hit($throttleKey, 300);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|min:3|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // Opsional: hanya berlaku kalau RECAPTCHA_ENABLED_FOR_API=true
            // dan RECAPTCHA_SECRET_KEY terisi.
            'recaptcha_token' => [new RecaptchaRule(forApi: true)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        $token = $user->createToken('authToken', TokenAbility::abilitiesForRole($user->role ?? 'user'))->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registered successfully and logged in',
            'token' => $token,
            'token_type' => 'Bearer',
            'data' => new UserResource($user)
        ], 201);
    }
}
