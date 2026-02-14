<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AiArticleGeneration;
use App\Jobs\GenerateAiArticleJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiGeneratorController extends Controller
{
    public function index(Request $request)
    {
        // Fetch history for the current user
        $history = AiArticleGeneration::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            return view('pages.dashboard.posts.partials.ai-generator-table', compact('history'))->render();
        }

        return view('pages.dashboard.posts.ai-generator', compact('history'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'language' => 'required|string|in:en,id,es,fr,de',
            'model' => 'required|string',
        ]);

        // Determine provider based on model selection
        $model = $request->input('model');
        $provider = 'gemini'; // Default

        // Simple logic to detect provider.
        // Adjust this logic if you have more specific rules or if the frontend sends the provider.
        if (in_array($model, ['deepseek-v3-2-251201', 'glm-4-7-251222', 'kimi-k2-250905', 'kimi-k2-thinking-251104', 'seed-1-8-251228'])) {
            $provider = 'sumopod';
        }

        $generation = AiArticleGeneration::create([
            'user_id' => Auth::id(),
            'topic' => $request->input('topic'),
            'language' => $request->input('language'),
            'model' => $model,
            'provider' => $provider,
            'status' => 'pending',
        ]);

        // Dispatch Job
        GenerateAiArticleJob::dispatch($generation->id)->onQueue('generator');

        return redirect()->route('admin.posts.ai-generator.index')->with('success', 'Generation started. Please wait for the process to complete.');
    }
}
