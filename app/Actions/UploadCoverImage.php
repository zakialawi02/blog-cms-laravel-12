<?php

namespace App\Actions;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UploadCoverImage
{
    /** Sisi terpanjang maksimum untuk versi _small (px). */
    private const SMALL_MAX = 500;

    /** Kualitas encode _small — berlaku untuk JPEG & WebP (PNG mengabaikannya). */
    private const SMALL_QUALITY = 70;

    /**
     * @param UploadedFile|null $file
     * @param string|null $oldImageUrl URL ke gambar versi _small
     * @return string|null
     * @throws Exception
     */
    public function execute(?UploadedFile $file, ?string $oldImageUrl = null): ?string
    {
        if (!$file) {
            return $oldImageUrl;
        }

        $baseFilename = time() . '_' . Str::random(20);
        $extension = \App\Support\ImageExtension::fromUpload($file, 'cover');

        $largeFilename = $baseFilename . '_large.' . $extension;
        $smallFilename = $baseFilename . '_small.' . $extension;

        $largeRelativePath = 'media/img/' . $largeFilename;
        $smallRelativePath = 'media/img/' . $smallFilename;

        if ($oldImageUrl) {
            $oldSmallFilename = basename($oldImageUrl);
            $oldLargeFilename = str_replace('_small.', '_large.', $oldSmallFilename);
            $oldFilePaths = [
                'media/img/' . $oldSmallFilename,
                'media/img/' . $oldLargeFilename,
            ];
            Storage::disk('public')->delete($oldFilePaths);
        }

        $disk = Storage::disk('public');
        $manager = new ImageManager(new Driver());

        try {
            // Versi _large: file asli dari user, disimpan APA ADANYA
            // (tanpa resize maupun kompresi).
            $disk->putFileAs('media/img', $file, $largeFilename);

            // Versi _small: maksimal 500x500 px, quality 70.
            // scaleDown() menjaga aspek rasio dan tidak memperbesar gambar
            // yang sudah lebih kecil dari 500 px (beda dengan scale()).
            $manager->read($file)
                ->scaleDown(width: self::SMALL_MAX, height: self::SMALL_MAX)
                ->save($disk->path($smallRelativePath), self::SMALL_QUALITY);
        } catch (Exception $e) {
            $disk->delete([$largeRelativePath, $smallRelativePath]);
            Log::error('Gagal membuat gambar terkompresi: ' . $e->getMessage());
            throw new Exception('Failed to compress and save cover image.');
        }

        return Storage::url($smallRelativePath);
    }
}
