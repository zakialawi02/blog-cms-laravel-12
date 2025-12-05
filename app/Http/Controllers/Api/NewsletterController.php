<?php

namespace App\Http\Controllers\Api;

use App\Models\Newsletter;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsletterResource;
use App\Http\Requests\Api\NewsletterRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NewsletterController extends Controller
{
    public function index(): JsonResponse
    {
        $newsletters = Newsletter::latest()->get();

        return response()->json([
            'success' => true,
            'data' => NewsletterResource::collection($newsletters),
        ]);
    }

    public function store(NewsletterRequest $request): JsonResponse
    {
        $newsletter = Newsletter::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Email subscribed',
            'data' => new NewsletterResource($newsletter),
        ], 201);
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $newsletter = Newsletter::findOrFail($id);
            $newsletter->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subscription removed',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found',
            ], 404);
        }
    }
}
