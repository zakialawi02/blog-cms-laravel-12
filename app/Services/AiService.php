<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;

class AiService
{
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-05-20:generateContent';
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');

        if (empty($this->apiKey)) {
            throw new Exception('GEMINI_API_KEY is not set in the .env file.');
        }
    }

    /**
     * Generates text based on the prompt.
     *
     * @param string $prompt
     * @return string
     * @throws Exception
     */
    public function generateText($prompt): string
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
            $response = Http::post($this->apiUrl . '?key=' . $this->apiKey, $payload);

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
     * Generates an image based on a prompt.
     *
     * @param string $prompt Description of the image to be created.
     * @return array Array containing image data in base64 format.
     * @throws Exception
     */
    public function generateImage(string $prompt): array
    {
        // Model ini khusus untuk bisa menghasilkan output gambar
        $model = 'models/gemini-2.0-flash-preview-image-generation:generateContent';

        // Membuat prompt lebih deskriptif untuk hasil yang lebih baik
        $payload = [
            'contents' => [
                'role' => "user",
                'parts' => [
                    // Memberi instruksi yang jelas untuk membuat gambar
                    ['text' => "Generate a photorealistic image of: " . $prompt]
                ]
            ],
            'generationConfig' => [
                // Konfigurasi untuk memastikan output adalah gambar
                "responseMimeType" => "image/png",
                "candidateCount" => 1
            ]
        ];

        $response = Http::post("{$this->apiUrl}/{$model}?key={$this->apiKey}", $payload);

        if ($response->failed()) {
            throw new Exception(
                'Failed to get an image response from the AI service. Details: ' . $response->body(),
                $response->status()
            );
        }

        $candidates = $response->json('candidates');

        if (empty($candidates)) {
            throw new Exception('The AI service returned no candidates for the image.');
        }

        $imageData = [];
        // Loop melalui semua kandidat dan ekstrak data gambarnya
        foreach ($candidates as $candidate) {
            $part = $candidate['content']['parts'][0] ?? null;
            // Pastikan part tersebut adalah inlineData (gambar) dan bukan teks
            if ($part && isset($part['inlineData']['data'])) {
                $imageData[] = $part['inlineData']['data']; // data gambar dalam format base64
            }
        }

        if (empty($imageData)) {
            throw new Exception('The AI service returned a response, but it did not contain image data.');
        }

        return $imageData;
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
     *
     * @return JsonResponse
     *
     * @throws Exception If the API request fails or the response is invalid.
     */
    public function generateArticle($prompt): JsonResponse
    {
        $promptTextOutput = "## AI ROLE ##
                You are an expert SEO Content Writer and a professional copywriter. Your task is to generate a high-quality, comprehensive, and engaging article on the topic provided below. You must strictly adhere to all instructions, especially the output format.

                ## PRIMARY TASK ##
                Write a long, comprehensive, and high-quality article in English (700-1500 words) based on the content guidelines and topic provided below.

                ## CONTENT GUIDELINES ##
                1.  **Target Audience:**
               Beginners, experienced, 15-45 years old
                2.  **Tone & Style:**
               informative, casual and friendly, and detailed
                3.  **Key Points to Cover (Outline):**
                    * Introduction explaining the importance of the topic.
                    * The article should have a clear flow. Include an introduction (without a heading), several main points discussed under `<h2>` or `<h3>` or `<h4>` headings, practical tips or examples, and a concluding summary paragraph.
                    * Use an <h2> for \"Common Challenges and How to Overcome Them\".
                    * A concluding paragraph that summarizes the main points and includes a call to action.
                4.  **Call to Action (CTA):** [Contoh: \"Share your own tips in the comments below!\", \"Start your journey today by trying one of these tips.\"]

                ## STRICT OUTPUT FORMATTING ##
                1.  The entire output must be a single, continuous block of HTML code without any line breaks ('\n') or Markdown.
                2.  Use the exact custom structure: <AiTitle>Your Title Here</AiTitle><AiMain>Your HTML content here</AiMain>.
                3.  Do not include `<html>`, `<head>`, `<body>`, or `<article>` tags.
                4.  The title in <AiTitle> must be catchy, SEO-friendly, include the primary keyword, and must not use a colon ':'.
                5.  The content in <AiMain> must start with an introductory paragraph (as specified in the outline)<p>, not a heading.
                6.  The largest heading used in the content must be <h2>. Use <h3>/<h4> for sub-headings, or other tags like <ul>, <ol>, <li>, <a>, <pre><code class=\"language-...\">, etc if needed.

                ## TOPIC INPUT ##
                Create an article in English about the following; $prompt";

        try {
            $result = $this->generateText($promptTextOutput);

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
