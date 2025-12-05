<?php

namespace App\Http\Controllers\Api;

use App\Models\RequestContributor;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\RequestContributorResource;
use App\Http\Requests\Api\RequestContributorRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RequestContributorController extends Controller
{
    public function index(): JsonResponse
    {
        $requests = RequestContributor::latest()->get();

        return response()->json([
            'success' => true,
            'data' => RequestContributorResource::collection($requests),
        ]);
    }

    public function store(RequestContributorRequest $request): JsonResponse
    {
        $requestContributor = RequestContributor::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Request submitted',
            'data' => new RequestContributorResource($requestContributor),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $requestContributor = RequestContributor::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new RequestContributorResource($requestContributor),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found',
            ], 404);
        }
    }

    public function update(RequestContributorRequest $request, string $id): JsonResponse
    {
        try {
            $requestContributor = RequestContributor::findOrFail($id);
            $requestContributor->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Request updated',
                'data' => new RequestContributorResource($requestContributor),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found',
            ], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $requestContributor = RequestContributor::findOrFail($id);
            $requestContributor->delete();

            return response()->json([
                'success' => true,
                'message' => 'Request deleted',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found',
            ], 404);
        }
    }
}
