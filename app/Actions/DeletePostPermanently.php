<?php

namespace App\Actions;

use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeletePostPermanently
{
    public function execute(Article $post): bool
    {
        // Delete cover images
        if ($post->cover) {
            $coverSmallFilename = basename($post->cover);
            $coverLargeFilename = str_replace('_small.', '_large.', $coverSmallFilename);
            $coverPaths = [
                'media/img/' . $coverSmallFilename,
                'media/img/' . $coverLargeFilename,
            ];

            foreach ($coverPaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                } else {
                    Log::warning("Cover file not found: {$path}");
                }
            }
        }

        // Delete content images (uploaded via CKEditor)
        $this->deleteContentImages($post->content);

        // Delete from database
        $post->forceDelete();
        return true;
    }

    /**
     * Extract and delete all uploaded images from post content HTML.
     *
     * Finds all image URLs pointing to media/uploads/ in the content
     * and removes them from storage.
     *
     * @param string|null $content
     * @return void
     */
    private function deleteContentImages(?string $content): void
    {
        if (empty($content)) {
            return;
        }

        // Match all image src attributes that point to our uploads folder
        if (preg_match_all('#/storage/(media/uploads/[^"\'?\s]+)#', $content, $matches)) {
            foreach ($matches[1] as $path) {
                // Security: skip paths with directory traversal
                if (str_contains($path, '..') || str_contains($path, "\0")) {
                    continue;
                }

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    Log::info("Deleted content image: {$path}");
                } else {
                    Log::warning("Content image not found: {$path}");
                }
            }
        }
    }
}
