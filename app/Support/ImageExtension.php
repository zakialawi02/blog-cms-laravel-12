<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

final class ImageExtension
{
    /** MIME terdeteksi => ekstensi yang kita izinkan. */
    private const MAP = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        'image/avif' => 'avif',
    ];

    /**
     * Ekstensi ditentukan dari MIME hasil deteksi konten file,
     * BUKAN dari ekstensi pada nama file kiriman client.
     */
    public static function fromUpload(UploadedFile $file, string $field = 'image'): string
    {
        $mime = $file->getMimeType();
        $ext = self::MAP[$mime] ?? null;

        if ($ext === null) {
            throw ValidationException::withMessages([
                $field => "Tipe file tidak diizinkan ({$mime}). Gunakan: jpg, png, webp, gif, avif.",
            ]);
        }

        return $ext;
    }
}
