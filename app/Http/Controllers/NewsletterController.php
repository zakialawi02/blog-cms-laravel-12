<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'title' => 'Newsletter',
        ];

        $newsletters = Newsletter::orderBy(request("sort_field", 'created_at'), request("sort_direction", "desc"))->paginate(25)->withQueryString();

        return view('pages.dashboard.newsletter.index', compact('data', 'newsletters'));
    }

    /**
     * Store a newly created resource in storage.
     * Ajax call/request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletters,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $newsletter = Newsletter::firstOrCreate(['email' => $request->email]);

            if ($newsletter->wasRecentlyCreated) {
                return response()->json([
                    'success' => true,
                    'message' => 'You have successfully subscribed to our newsletter.'
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'This email address is already subscribed.'
            ], 409); // 409 Conflict
        } catch (Exception $e) {
            Log::error('Newsletter subscription failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();

        return redirect()->back()->with('success', 'Newsletter deleted successfully');
    }
}
