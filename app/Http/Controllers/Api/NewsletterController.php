<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsletterResource;
use Illuminate\Database\QueryException;
use App\Http\Requests\Api\StoreNewsletterRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Newsletter::query();

            // 1. Search / Filtering
            if ($request->has('search')) {
                $search = $request->query('search');
                $query->where('email', 'like', "%{$search}%");
            }

            // 2. Filtering by subscription status
            if ($request->has('subscribed')) {
                $subscribed = $request->query('subscribed');
                if ($subscribed === 'true') {
                    $query->where('is_subscribed', true);
                } elseif ($subscribed === 'false') {
                    $query->where('is_subscribed', false);
                }
            }

            // 3. Sorting
            $sort = $request->query('sort', 'created_at');
            $direction = $request->query('direction', 'desc');

            $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'desc';
            $allowedSorts = ['id', 'email', 'is_subscribed', 'created_at', 'updated_at'];

            if (in_array($sort, $allowedSorts)) {
                $query->orderBy($sort, $direction);
            }

            // 4. Pagination Limit
            $limit = $request->query('limit', 10);
            $limit = (is_numeric($limit) && $limit > 0) ? (int) $limit : 10;

            // Execute Paginated Query Appending Query Strings
            $newsletters = $query->paginate($limit)->appends($request->query());

            return NewsletterResource::collection($newsletters)->additional([
                'success' => true,
                'message' => 'List of all newsletter subscribers',
            ])->response()->setStatusCode(Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $newsletter = Newsletter::where('email', $request->email)->first();

            if ($newsletter) {
                if ($newsletter->is_subscribed) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This email address is already subscribed.',
                    ], Response::HTTP_CONFLICT);
                }

                // Resubscribe - update is_subscribed from 0 to 1
                $newsletter->update(['is_subscribed' => true]);

                return response()->json([
                    'success' => true,
                    'message' => 'You have successfully subscribed to our newsletter.',
                    'data' => new NewsletterResource($newsletter),
                ], Response::HTTP_OK);
            }

            // Create new subscription
            $newsletter = Newsletter::create([
                'email' => $request->email,
                'is_subscribed' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'You have successfully subscribed to our newsletter.',
                'data' => new NewsletterResource($newsletter),
            ], Response::HTTP_CREATED);

        } catch (Exception $e) {
            Log::error('Newsletter subscription failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     * (Soft delete - set is_subscribed to false)
     * Admin only endpoint
     */
    public function destroy($id): JsonResponse
    {
        try {
            $newsletter = Newsletter::findOrFail($id);

            // Soft unsubscribe - set is_subscribed to false
            $newsletter->update(['is_subscribed' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Subscriber has been unsubscribed successfully.',
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subscriber not found.',
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Unsubscribe from newsletter (self-service with signed URL)
     * Security: Requires valid signed URL
     */
    public function unsubscribe(Request $request, Newsletter $newsletter): JsonResponse
    {
        try {
            // Validate signed URL
            if (!$request->hasValidSignature()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired signature.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Unsubscribe the user
            $newsletter->update(['is_subscribed' => false]);

            return response()->json([
                'success' => true,
                'message' => 'You have been successfully unsubscribed from our newsletter.',
                'data' => new NewsletterResource($newsletter),
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('Newsletter unsubscribe failed', [
                'error' => $th->getMessage(),
                'newsletter_id' => $newsletter->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Resubscribe to newsletter (self-service with signed URL)
     * Security: Requires valid signed URL
     */
    public function resubscribe(Request $request, Newsletter $newsletter): JsonResponse
    {
        try {
            // Validate signed URL
            if (!$request->hasValidSignature()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired signature.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Resubscribe the user
            $newsletter->update(['is_subscribed' => true]);

            return response()->json([
                'success' => true,
                'message' => 'You have successfully resubscribed to our newsletter.',
                'data' => new NewsletterResource($newsletter),
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('Newsletter resubscribe failed', [
                'error' => $th->getMessage(),
                'newsletter_id' => $newsletter->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
