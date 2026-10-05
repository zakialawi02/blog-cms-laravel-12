<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

final class ImageExtension
{
    /**
     * MIME terdeteksi => ekstensi yang kita izinkan.
     *
     * Yang sengaja TIDAK diizinkan:
     * - AVIF: driver GD di server ini tidak bisa mendekode-nya.
     * - GIF : bisa berisi animasi, sedangkan GD hanya membaca frame pertama,
     *         sehingga hasil thumbnail/kompresi tidak mewakili file aslinya.
     */
    private const MAP = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * Daftar ekstensi yang diizinkan, untuk pesan error & validasi.
     *
     * @return array<int, string>
     */
    public static function allowed(): array
    {
        return array_values(self::MAP);
    }

    /**
     * Daftar ekstensi untuk pesan error. "jpg" ditulis "jpg/jpeg" supaya
     * pengguna tahu keduanya diterima (keduanya terdeteksi sebagai image/jpeg).
     *
     * @return array<int, string>
     */
    public static function allowedForHumans(): array
    {
        return array_map(
            fn (string $extension) => $extension === 'jpg' ? 'jpg/jpeg' : $extension,
            self::allowed()
        );
    }

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
                $field => sprintf(
                    'Tipe file tidak diizinkan (%s). Gunakan: %s.',
                    $mime,
                    implode(', ', self::allowedForHumans())
                ),
            ]);
        }

        return $ext;
    }
}
