<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;

class AiService
{
    protected $geminiTextApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-05-20:generateContent';
    protected $geminiImageApiUrl = 'https://generativelanguage.googleapis.com/v1beta/imagen-3.0-generate-002:predict';
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');

        if (empty($this->apiKey)) {
            Log::error('GEMINI_API_KEY is not set in the .env file.');
        }
    }

    /**
     * Generates text based on the prompt.
     *
     * @param string $prompt
     * @return string
     * @throws Exception
     */
    public function textToText($prompt): string
    {
        // Payload structure for Gemini API
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
            $response = Http::post($this->geminiTextApiUrl . '?key=' . $this->apiKey, $payload);

            if ($response->failed()) {
                Log::error('AI Service API Error', ['response' => $response->json()]);
                throw new Exception('Failed to get a valid response from the AI service.', $response->status());
            }

            $text = $response->json('candidates.0.content.parts.0.text');

            if (is_null($text)) {
                Log::warning('AI Service returned empty content', ['response' => $response->json()]);
                throw new Exception('The AI service returned an empty response.');
            }

            return $text;
        } catch (ConnectionException $e) {
            // Menangani error koneksi (misal: DNS, timeout)
            Log::critical('AI Service Connection Failed', ['error' => $e->getMessage()]);
            throw new Exception('Could not connect to the AI service.', 503, $e);
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
        $fullUrl = $this->geminiImageApiUrl . '?key=' . $this->apiKey;

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
     * Generates an article based on the given prompt.
     *
     * The response is a JSON object with the following structure:
     * {
     *     "success": boolean,
     *     "message": string,
     *     "data": string // the generated article in HTML format
     * }
     *
     * The article is generated using the Gemini API.
     *
     * @param string $prompt The topic of the article to generate.
     * @return JsonResponse
     * @throws Exception If the API request fails or the response is invalid.
     */
    public function generateArticle($prompt): JsonResponse
    {
        $promptTextOutput = "## AI ROLE ##
            You are an expert SEO Content Writer and a professional keyword analyst. You are skilled in writing technical articles. Your task is to analyze the given topic, generate relevant SEO keywords, and then write a high-quality, comprehensive article purely in HTML format. You must strictly adhere to all instructions, especially the output format, the prohibition of Markdown, and the formatting for code snippets.

            ## PRIMARY TASK ##
            1.  **Analyze the Topic:** First, analyze the topic from the '## TOPIC INPUT ##' section to identify the core subject, user intent, and main keywords.
            2.  **Generate Keywords:** Based on your analysis, generate a list of 5-10 relevant SEO keywords.
            3.  **Write the Article:** Write a long, comprehensive article in English (800-1800 words) using the keywords you identified, following all content and formatting guidelines.

            ## CONTENT GUIDELINES ##
            1.  **Target Audience:**
            Beginners and experienced individuals, aged 15-45.
            2.  **Tone & Style:**
            Informative, casual, friendly, and detailed.
            3.  **Content Outline:**
                * The article must start with an engaging introduction that explains the importance of the topic and includes the most important keyword you identified.
                * The content should flow logically. Use the keywords you generated naturally in `<h2>` or `<h3>` headings and throughout the article body.
                * Include a specific heading: `<h2>Common Challenges and How to Overcome Them</h2>`.
                * Incorporate practical tips or examples. If the topic is technical, include relevant code snippets using the correct format specified in the output rules.
                * End with a strong concluding paragraph that summarizes the main points and includes a call to action.
            4.  **Call to Action (CTA):**
            Include a CTA like \"Share your own tips in the comments below!\" or \"Start your journey today by trying one of these tips.\" or what you like. The CTA must be near the end of the article.

            ## STRICT OUTPUT FORMATTING ##
            1.  The entire output must be a single, continuous block of text. Do not use Markdown anywhere.
            2.  Use the exact custom structure: <AiTitle>Your Title Here</AiTitle><AiSEOKeyword>Your Keywords Here</AiSEOKeyword><AiMain>Your HTML content here</AiMain>.
            3.  Do not include `<html>`, `<head>`, `<body>`, or `<article>` tags.
            4.  **<AiTitle>:** Must be a catchy, SEO-friendly title that uses the primary keyword you identified from the topic. Do not use a colon ':'.
            5.  **<AiSEOKeyword>:** Place 5-20 SEO keywords that you have created here. Keywords must be separated by commas, without spaces after the commas (e.g., keyword1, keyword2, keyword3). Keywords should be words, not phrases.
            6.  **<AiMain>:** Must contain the full article content in valid, clean HTML.
                * It must start with an introductory paragraph inside `<p>` tags.
                * The largest heading used must be `<h2>`.
                * **NO MARKDOWN:** All content inside this tag must be pure HTML. clear \n \n\n .
                * **CODE SNIPPETS:** If the article requires code examples, they MUST be wrapped in `<pre><code class=\"language-xxx\">` tags, where `xxx` is the specific language name (e.g., `language-html`, `language-javascript`, `language-css`, `language-python`). This is mandatory for all code blocks.

            ## TOPIC INPUT ##
            Create an article about the following topic. Remember to first analyze it for keywords and to write the entire output in clean HTML without any Markdown elements, following all code snippet formatting rules if applicable.
            Topic: $prompt";

        try {
            $result = $this->textToText($promptTextOutput);

            return response()->json([
                'success' => true,
                'message' => 'Text generated successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }
}
