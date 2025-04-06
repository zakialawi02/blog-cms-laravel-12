<?php

namespace App\Actions;

use App\Models\Article;
use Illuminate\Support\Facades\Log;

class DeletePostPermanently
{
    public function execute(Article $post): bool
    {        // Hapus file cover jika ada
        if ($post->cover) {
            $coverPath = public_path('media/img/' . basename($post->cover));

            if (file_exists($coverPath)) {
                unlink($coverPath);
            } else {
                Log::warning("Gagal hapus file cover: tidak ditemukan di path {$coverPath}");
            }
        }

        // Hapus data dari database
        $post->forceDelete();
        return true;
    }
}
