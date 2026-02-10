<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Upload image from CKEditor WYSIWYG.
     *
     * Validates the uploaded file (image types, max 2MB),
     * stores it to public disk under media/uploads/,
     * and returns the URL in the format CKEditor expects.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
        ]);

        try {
            $file = $request->file('upload');
            $filename = time() . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();

            Storage::disk('public')->putFileAs('media/uploads', $file, $filename);

            $url = Storage::disk('public')->url('media/uploads/' . $filename);

            return response()->json([
                'url' => $url,
            ]);
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());

            return response()->json([
                'error' => [
                    'message' => 'Image upload failed.',
                ],
            ], 500);
        }
    }

    /**
     * Delete an uploaded image from storage.
     *
     * Accepts the image URL, extracts the relative path,
     * and removes the file from the public disk.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|string',
        ]);

        try {
            $url = $request->input('url');

            // Extract relative path from URL (e.g., "media/uploads/filename.jpg")
            $path = $this->extractStoragePath($url);

            if (!$path || !Storage::disk('public')->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found.',
                ], 404);
            }

            Storage::disk('public')->delete($path);

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Image delete failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image.',
            ], 500);
        }
    }

    /**
     * Extract the storage-relative path from a full URL.
     *
     * Expects URLs like: http://host/storage/media/uploads/filename.jpg
     * Returns: media/uploads/filename.jpg
     *
     * @param  string  $url
     * @return string|null
     */
    private function extractStoragePath(string $url): ?string
    {
        // Only allow files in the media/uploads directory for security
        if (preg_match('/\/storage\/(media\/uploads\/[^?\s"\']+)/', $url, $matches)) {
            $path = $matches[1];

            // Security: prevent directory traversal
            if (str_contains($path, '..') || str_contains($path, "\0")) {
                return null;
            }

            return $path;
        }

        return null;
    }
}
