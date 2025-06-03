<?php

namespace App\Actions;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadCoverImage
{
    public function execute(?UploadedFile $file, ?string $oldImageUrl = null): ?string
    {
        if (!$file) {
            return $oldImageUrl;
        }

        $filename = time() . '_' . Str::random(20) . '.' . $file->getClientOriginalExtension();
        $relativePath = 'media/img/' . $filename;

        // Hapus file lama jika ada
        if ($oldImageUrl) {
            $oldFilename = basename($oldImageUrl);
            $oldFilePath = 'media/img/' . $oldFilename;

            if (Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
            } else {
                Log::warning("File cover lama tidak ditemukan: " . $oldFilePath);
            }
        }

        // Simpan file baru
        $stored = Storage::disk('public')->putFileAs('media/img', $file, $filename);
        if (!$stored) {
            throw new Exception('Failed to upload cover image.');
        }

        // Return URL publik ke file-nya
        return Storage::url($relativePath);
    }
}
