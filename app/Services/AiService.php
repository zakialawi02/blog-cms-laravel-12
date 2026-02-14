<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;

class AiService
{
    protected $geminiTextApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent';
    protected $geminiImageApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/imagen-4.0-generate-001:predict';
    // protected $geminiImageApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-image:generateContent';
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
     * Public method to be called by the Controller, returning a JsonResponse.
     *
     * This method is responsible for generating a long, comprehensive article in HTML format using the provided topic.
     * It internally calls the `textToText` method and handles any potential exceptions.
     *
     * @param string $prompt The topic to generate the article about.
     * @param mixed|null $exsistData Existing article data to be used as an additional reference source.
     * @return JsonResponse A successful response contains the generated article in HTML format.
     */
    public function generateArticle($prompt, $exsistData = null): JsonResponse
    {
        $promptTextOutput = "## AI ROLE ##
        You are an expert SEO Content Writer and a professional keyword analyst. You are skilled in writing technical articles. Your task is to analyze the given topic, generate relevant SEO keywords, and then write a high-quality, comprehensive article purely in HTML format. You must strictly adhere to all instructions, especially the output format, the prohibition of Markdown, and the formatting for code snippets.

        ## PRIMARY TASK ##
        1.  **Analyze the Topic:** First, analyze the topic from the '## TOPIC INPUT ##' section to identify the core subject, user intent, and main keywords.
        2.  **Generate Keywords:** Based on your analysis, generate a list of 5-10 relevant SEO keywords.
        3.  **Write the Article:** Write a long, comprehensive article in English (800-1800 words or more) using the keywords you identified, following all content and formatting guidelines.

        ## CONTENT GUIDELINES ##
        1.  **Target Audience:**
        Beginners and experienced individuals, aged 15-45.
        2.  **Tone & Style:**
        Informative, casual, friendly, and detailed.
        3.  **Content Outline:**

        - The article must start with an engaging introduction that explains the importance of the topic and includes the most important keyword you identified.
        - The content should flow logically. Use the keywords you generated naturally in `<h2>` or `<h3>` headings and throughout the article body.
        - Include a specific heading: `<h2>Common Challenges and How to Overcome Them</h2>`.
        - Incorporate practical tips or examples. If the topic is technical, include relevant code snippets using the correct format specified in the output rules.
        - End with a strong concluding paragraph that summarizes the main points and includes a call to action.

        4.  **Call to Action (CTA):**
        Include a call to action (CTA) such as \"Share your own tips in the comments section below!\" or \"Start your journey today by trying one of these tips.\" or any other phrase you like. The CTA should be placed at the end of the article.

        ## EXISTING DATA INSTRUCTION ##
        If there is an existing article data available in the variable named `$exsistData`, you must carefully review it and, if the content is relevant and aligned with the current topic, use it as an additional reference source while writing your new article. You are encouraged to paraphrase, extract facts, or repurpose key points from this existing data where appropriate to enhance and enrich the new article, while still following all guidelines and ensuring originality and freshness of the content.

        ## STRICT OUTPUT FORMATTING ##
        1.  The entire output must be a single, continuous block of text. Do not use Markdown anywhere.
        2.  Use the exact custom structure: <AiTitle>Your Title Here</AiTitle><AiSEOKeyword>Your Keywords Here</AiSEOKeyword><AiMain>Your HTML content here</AiMain>.
        3.  Do not include `<html>`, `<head>`, `<body>`, or `<article>` tags.
        4.  **<AiTitle>:** Must be a catchy, SEO-friendly title that uses the primary keyword you identified from the topic. Do not use a colon ':'.
        5.  **<AiSEOKeyword>:** Place 5-20 SEO keywords that you have created here. Keywords must be separated by commas (e.g., keyword 1, keyword 2, keyword tiga). Keywords should be words, not phrases.
        6.  **<AiMain>:** Must contain the full article content in valid, clean HTML.

        - It must start with an introductory paragraph inside `<p>` tags.
        - The largest heading used must be `<h2>`.
        - **NO MARKDOWN:** All content inside this tag must be pure HTML. clear \n \n\n .
        - **CODE SNIPPETS:** If the article requires code examples, they MUST be wrapped in `<pre><code class=\"language-xxx\">` tags, where `xxx` is the specific language name (e.g., `language-html`, `language-javascript`, `language-css`, `language-python`). This is mandatory for all code blocks.

        ## TOPIC INPUT ##
        Create an article about the following topic. Remember to first analyze it for keywords and to write the entire output in clean HTML without any Markdown elements, following all code snippet formatting rules if applicable.
        Topic or command: $prompt";


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
