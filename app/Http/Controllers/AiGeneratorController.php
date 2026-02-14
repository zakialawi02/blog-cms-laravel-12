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

        $categories = \App\Models\Category::all();

        return view('pages.dashboard.posts.ai-generator', compact('history', 'categories'));
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

        // Detect provider from config
        foreach (config('ai.models') as $providerKey => $models) {
            if (array_key_exists($model, $models)) {
                $provider = $providerKey;
                break;
            }
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

    public function generateIdeas(Request $request, \App\Services\AiService $aiService)
    {
        $request->validate([
            'category' => 'required|string|max:255',
        ]);

        $category = $request->input('category');
        $result = $aiService->generateTopicIdeas($category);

        return response()->json($result);
    }
}
