<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;

class AiService
{
    protected $sumopodApiUrl = 'https://ai.sumopod.com/v1/chat/completions';
    protected $geminiTextApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
    protected $geminiImageApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/imagen-4.0-generate-001:predict';
    protected $qwenApiUrl = 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1/chat/completions';
    protected $geminiApiKey;
    protected $sumopodApiKey;
    protected $qwenApiKey;
    protected $cloudflareApiToken;
    protected $cloudflareAccountId;
    protected $timeout = 600;
    protected $allowedModels = [];

    public function __construct()
    {
        $this->geminiApiKey = env('GEMINI_API_KEY');
        $this->sumopodApiKey = env('SUMOPOD_API_KEY');
        $this->qwenApiKey = env('DASHSCOPE_API_KEY');
        $this->cloudflareApiToken = env('CLOUDFLARE_AI_API_TOKEN');
        $this->cloudflareAccountId = env('CLOUDFLARE_ACCOUNT_ID');

        // Build allowed models dynamically from config
        foreach (config('ai.models', []) as $models) {
            $this->allowedModels = array_merge($this->allowedModels, array_keys($models));
        }

        if (empty($this->geminiApiKey)) {
            Log::warning('GEMINI_API_KEY is not set in the .env file.');
        }
        if (empty($this->sumopodApiKey)) {
            Log::warning('SUMOPOD_API_KEY is not set in the .env file.');
        }
        if (empty($this->qwenApiKey)) {
            Log::warning('DASHSCOPE_API_KEY is not set in the .env file.');
        }
        if (empty($this->cloudflareApiToken) || empty($this->cloudflareAccountId)) {
            Log::warning('CLOUDFLARE_AI_API_TOKEN or CLOUDFLARE_ACCOUNT_ID is not set in the .env file.');
        }
    }

    /**
     * Generates text based on the prompt using specified provider and model.
     *
     * @param string $prompt
     * @param string $model
     * @param string $provider
     * @return string
     * @throws Exception
     */
    public function textToText($prompt, $model = 'gemini-3-flash-preview', $provider = 'gemini'): string
    {
        if ($provider === 'sumopod') {
            return $this->generateWithSumopod($prompt, $model);
        }

        if ($provider === 'qwen') {
            return $this->generateWithQwen($prompt, $model);
        }

        if ($provider === 'cloudflare') {
            return $this->generateWithCloudflare($prompt, $model);
        }

        return $this->generateWithGemini($prompt, $model);
    }

    protected function generateWithGemini($prompt, $model)
    {
        // Ensure model name is correct for Gemini URL
        // Default to a known model if generic "gemini" is passed or if empty
        if (empty($model) || $model === 'gemini') {
            $model = 'gemini-3-flash-preview';
        }

        $url = $this->geminiTextApiUrl . $model . ':generateContent?key=' . $this->geminiApiKey;

        $payload = [
            'contents' => [
                [
                    'role' => "user",
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->post($url, $payload);

            if ($response->failed()) {
                Log::error('Gemini API Error', ['response' => $response->json(), 'url' => $url]);
                throw new Exception('Failed to get a valid response from Gemini: ' . $response->body(), $response->status());
            }

            $text = $response->json('candidates.0.content.parts.0.text');

            if (is_null($text)) {
                Log::warning('Gemini returned empty content', ['response' => $response->json()]);
                throw new Exception('Gemini returned an empty response.');
            }

            return $text;
        } catch (ConnectionException $e) {
            Log::critical('Gemini Connection Failed', ['error' => $e->getMessage()]);
            throw new Exception('Could not connect to Gemini service.', 503, $e);
        }
    }

    protected function generateWithSumopod($prompt, $model)
    {
        if (empty($model)) {
            $model = 'gpt-4o-mini'; // Default fallback
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->sumopodApiKey,
                ])->post($this->sumopodApiUrl, [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->failed()) {
                Log::error('Sumopod API Error', ['response' => $response->json()]);
                throw new Exception('Failed to get a valid response from Sumopod: ' . $response->body(), $response->status());
            }

            $text = $response->json('choices.0.message.content');

            if (is_null($text)) {
                Log::warning('Sumopod returned empty content', ['response' => $response->json()]);
                throw new Exception('Sumopod returned an empty response.');
            }

            return $text;
        } catch (ConnectionException $e) {
            Log::critical('Sumopod Connection Failed', ['error' => $e->getMessage()]);
            throw new Exception('Could not connect to Sumopod service.', 503, $e);
        }
    }

    protected function generateWithQwen($prompt, $model)
    {
        if (empty($model)) {
            $model = 'qwen3.5-plus';
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->qwenApiKey,
                ])->post($this->qwenApiUrl, [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'stream' => false,
                    'enable_thinking' => true,
                ]);

            if ($response->failed()) {
                Log::error('Qwen API Error', ['response' => $response->json()]);
                throw new Exception('Failed to get a valid response from Qwen: ' . $response->body(), $response->status());
            }

            $text = $response->json('choices.0.message.content');

            if (is_null($text)) {
                Log::warning('Qwen returned empty content', ['response' => $response->json()]);
                throw new Exception('Qwen returned an empty response.');
            }

            return $text;
        } catch (ConnectionException $e) {
            Log::critical('Qwen Connection Failed', ['error' => $e->getMessage()]);
            throw new Exception('Could not connect to Qwen service.', 503, $e);
        }
    }

    protected function generateWithCloudflare($prompt, $model)
    {
        if (empty($model)) {
            $model = '@cf/zai-org/glm-4.7-flash';
        }

        $url = 'https://api.cloudflare.com/client/v4/accounts/' . $this->cloudflareAccountId . '/ai/run/' . $model;

        try {
            $response = Http::withoutVerifying()->timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->cloudflareApiToken,
                ])->post($url, [
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Cloudflare AI API Error', ['response' => $response->json()]);
                throw new Exception('Failed to get a valid response from Cloudflare AI: ' . $response->body(), $response->status());
            }
            $text = $response->json('result.choices.0.message.content');

            if (is_null($text)) {
                Log::warning('Cloudflare AI returned empty content', ['response' => $response->json()]);
                throw new Exception('Cloudflare AI returned an empty response.');
            }

            return $text;
        } catch (ConnectionException $e) {
            Log::critical('Cloudflare AI Connection Failed', ['error' => $e->getMessage()]);
            throw new Exception('Could not connect to Cloudflare AI service.', 503, $e);
        }
    }

    /**
     * Generates an image based on the text prompt using the Gemini API.
     *
     * @param string $prompt The description of the image to generate.
     * @return array An associative array with success status, message, and base64 encoded image data.
     * @throws Exception
     */
    public function textToImage(string $prompt): array
    {
        // Buat URL lengkap dengan API Key
        $fullUrl = $this->geminiImageApiUrl . '?key=' . $this->geminiApiKey;

        // Struktur payload untuk model image generation Gemini
        $payload = [
            'contents' => [
                [
                    'role' => "user",
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::post($fullUrl, $payload);

            if ($response->failed()) {
                $errorDetails = $response->json() ?? ['error' => ['message' => $response->body()]];
                Log::error('Gemini Image API Error', [
                    'status' => $response->status(),
                    'response' => $errorDetails
                ]);
                $errorMessage = $errorDetails['error']['message'] ?? 'Failed to get a valid response from the Gemini image service.';
                throw new Exception($errorMessage, $response->status());
            }

            // Ekstrak data gambar base64 dari response.
            // Path ini umum untuk respons gambar dari Gemini API.
            $imageData = $response->json('candidates.0.content.parts.0.inlineData.data');

            if (is_null($imageData)) {
                Log::warning('Gemini Image API returned empty content', ['response' => $response->json()]);
                throw new Exception('The AI service returned an empty image response.');
            }

            return [
                'success' => true,
                'message' => 'Image generated successfully from Gemini.',
                'data' => [
                    'image_base64' => $imageData,
                    'format' => $response->json('candidates.0.content.parts.0.inlineData.mimeType', 'image/png'),
                ],
            ];
        } catch (ConnectionException $e) {
            Log::critical('Gemini Image Service Connection Failed', ['error' => $e->getMessage()]);
            throw new Exception('Could not connect to the Gemini image service.', 503, $e);
        }
    }

    /**
     * Public method to be called by the Controller, returning a JsonResponse.
     *
     * @param string $prompt
     * @return JsonResponse
     */
    public function generateImage($prompt): JsonResponse
    {
        try {
            // Panggil metode inti untuk mendapatkan hasil dalam bentuk array
            $result = $this->textToImage($prompt);

            // Kembalikan hasil sebagai JsonResponse yang sukses
            return response()->json($result, 200);
        } catch (Exception $e) {
            // Tangani semua jenis exception yang mungkin terjadi
            $statusCode = is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $statusCode);
        }
    }

    /**
     * Public method to be called by the Controller, returning a JsonResponse.
     *
     * This method is responsible for generating a long, comprehensive article in HTML format using the provided topic.
     * It internally calls the `textToText` method and handles any potential exceptions.
     *
     * @param string $prompt The topic to generate the article about.
     * @param string $language The language to generate the article in.
     * @param string $model The model to use.
     * @param string $provider The provider to use.
     * @param mixed|null $exsistData Existing article data to be used as an additional reference source.
     * @return array Returns array with success boolean and data/message.
     */
    public function generateArticle($prompt, $language = 'en', $model = 'gemini-3-flash-preview', $provider = 'gemini', $exsistData = null): array
    {
        if (!in_array($model, $this->allowedModels)) {
            return [
                'success' => false,
                'message' => "Invalid model selected: {$model}",
            ];
        }

        $languageName = match ($language) {
            'id' => 'Indonesian (Bahasa Indonesia)',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            default => 'English',
        };

        $promptTextOutput = "## AI ROLE ##
        You are an expert SEO Content Writer and a professional keyword analyst. You are skilled in writing technical articles. Your task is to analyze the given topic, generate relevant SEO keywords, and then write a high-quality, comprehensive article purely in HTML format. You must strictly adhere to all instructions, especially the output format, the prohibition of Markdown, and the formatting for code snippets. Make the writing slightly informal in some places, as human writers naturally do.

        ## PRIMARY TASK ##
        1.  **Analyze the Topic:** First, analyze the topic from the '## TOPIC INPUT ##' section to identify the core subject, user intent, and main keywords.
        2.  **Generate Keywords:** Based on your analysis, generate a list of 5-10 relevant SEO keywords.
        3.  **Write the Article:** Write a long, comprehensive article in {$languageName} (minimal 2000 words or more) using the keywords you identified, following all content and formatting guidelines. If you are below minimal words, continue by adding more relevant subheadings, examples, or deeper explanations until the minimum is reached.

        ## CONTENT GUIDELINES ##
        1.  **Target Audience:**
        Beginners and experienced individuals, aged 15-45.
        2.  **Tone & Style:**
        Informative, casual, friendly, and detailed.
        3.  **Content Outline:**

        - The article must start with an engaging introduction that explains the importance of the topic and includes the most important keyword you identified.
        - The content should flow logically. Use the keywords you generated naturally in `<h2>` or `<h3>` headings and throughout the article body.
        - Include a specific heading: `<h2>Common Challenges and How to Overcome Them</h2>` (Translated to {$languageName}).
        - Incorporate practical tips or examples. If the topic is technical, include relevant code snippets using the correct format specified in the output rules.
        - End with a strong concluding paragraph that summarizes the main points and includes a call to action.

        4.  **Call to Action (CTA):**
        Include a call to action (CTA) at the end of the article.

        ## EXISTING DATA INSTRUCTION ##
        If there is an existing article data available in the variable named `$exsistData`, you must carefully review it and, if the content is relevant and aligned with the current topic, use it as an additional reference source while writing your new article. You are encouraged to paraphrase, extract facts, or repurpose key points from this existing data where appropriate to enhance and enrich the new article, while still following all guidelines and ensuring originality and freshness of the content.

        ## STRICT OUTPUT FORMATTING ##
        1.  The entire output must be a single, continuous block of text. Do not use Markdown anywhere.
        2.  Use the exact custom structure: <AiTitle>Your Title Here</AiTitle><AiSEOKeyword>Your Keywords Here</AiSEOKeyword><AiMetaDescription>Your Meta Description Here</AiMetaDescription><AiMain>Your HTML content here</AiMain>.
        3.  Do not include `<html>`, `<head>`, `<body>`, or `<article>` tags.
        4.  **<AiTitle>:** Must be a catchy, SEO-friendly title that uses the primary keyword you identified from the topic. Do not use a colon ':'.
        5.  **<AiSEOKeyword>:** Place 5-10 SEO keywords that you have created here. Keywords must be separated by commas.
        6.  **<AiMetaDescription>:** Write a concise and compelling meta description (150-160 characters) that summarizes the article and includes the main keyword.
        7.  **<AiMain>:** Must contain the full article content in valid, clean HTML.
        8.  **NO MARKDOWN:** All content inside this tag must be pure HTML.
        9.  **CODE SNIPPETS (If any):** If the article has code examples, they MUST be wrapped in `<pre><code class=\"language-xxx\">` tags.

        ## HUMAN WRITING EMULATION MODE ##
        You must write in a completely human-like style.
        The article must:
        - Feel natural and organic
        - Avoid robotic or generic phrasing
        - Use varied sentence lengths
        - Include realistic explanations and examples
        - Include subtle opinions and natural commentary
        - Avoid repetitive structure

        The reader must not be able to tell this was generated by AI.

        ## TOPIC INPUT ##
        Create an article about the following topic and language. Remember to first analyze it for keywords and to write the entire output in clean HTML without any Markdown elements, following all code snippet formatting rules if applicable.
        Topic or command: $prompt";


        try {
            $result = $this->textToText($promptTextOutput, $model, $provider);

            return [
                'success' => true,
                'message' => 'Text generated successfully',
                'data' => $result,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generates a list of article topic ideas based on a category.
     *
     * @param string $category
     * @return array
     */
    public function generateTopicIdeas(string $category): array
    {
        $prompt = "Generate 5 catchy, SEO-friendly article topic titles related to the category '{$category}'. Return ONLY the titles in English, separated by a newline. Do not include numbering or bullet points.";

        try {
            // Use a random model and provider for this task
            $randomModel = $this->allowedModels[array_rand($this->allowedModels)];
            $provider = str_contains($randomModel, 'gemini') ? 'gemini' : 'sumopod';

            $text = $this->textToText($prompt, $randomModel, $provider);

            // Split by newline and filter empty lines
            $ideas = array_filter(array_map('trim', explode("\n", $text)));

            return [
                'success' => true,
                'data' => array_values($ideas)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
