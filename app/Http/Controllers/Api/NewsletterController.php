<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:newsletters,email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $newsletter = Newsletter::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Subscription stored successfully',
            'data' => $newsletter,
        ], Response::HTTP_CREATED);
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'List of newsletter subscribers',
            'data' => Newsletter::latest()->get(['id', 'email', 'created_at']),
        ]);
    }

    public function destroy(Newsletter $newsletter): JsonResponse
    {
        $newsletter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscriber removed successfully',
        ]);
    }
}
