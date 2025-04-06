<?php

namespace App\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class UploadCoverImage
{
    public function execute(?UploadedFile $file, ?string $oldImageUrl = null): ?string
    {
        if (!$file) {
            return $oldImageUrl;
        }

        $filename = time() . '_' . Str::random(20) . '.' . $file->getClientOriginalExtension();
        $relativePath = 'media/img/' . $filename;
        $destinationPath = public_path('media/img');

        // Hapus file lama jika ada
        if ($oldImageUrl) {
            $oldFilename = basename($oldImageUrl);
            $oldFilePath = public_path('media/img/' . $oldFilename);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            } else {
                Log::warning("File cover lama tidak ditemukan: " . $oldFilePath);
            }
        }

        $copied = copy($file->getRealPath(), $destinationPath . '/' . $filename);
        if (!$copied) {
            throw new Exception('Failed to upload cover image.');
        }

        return asset($relativePath);
    }
}
