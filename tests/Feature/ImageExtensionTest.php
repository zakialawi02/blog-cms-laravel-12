<?php

use App\Http\Requests\Api\StoreArticleRequest;
use App\Support\ImageExtension;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Helper: UploadedFile palsu dengan MIME & ekstensi tebakan yang ditentukan,
 * sehingga bisa mensimulasikan file AVIF tanpa perlu biner AVIF asli.
 */
function fakeUploadWith(string $guessedExtension, string $mime): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'imgext_');
    file_put_contents($path, 'dummy-content');

    // Bersihkan file sementara begitu proses test selesai, supaya /tmp
    // tidak menumpuk sisa dari setiap kali test dijalankan.
    register_shutdown_function(static fn () => @unlink($path));

    return new class(
        $path,
        'file.' . $guessedExtension,
        $mime,
        null,
        true,
        $guessedExtension
    ) extends UploadedFile {
        public function __construct(
            string $path,
            string $originalName,
            private string $forcedMime,
            ?int $error,
            bool $test,
            private string $forcedExtension
        ) {
            parent::__construct($path, $originalName, $forcedMime, $error, $test);
        }

        public function getMimeType(): ?string
        {
            return $this->forcedMime;
        }

        public function guessExtension(): ?string
        {
            return $this->forcedExtension;
        }
    };
}

it('accepts every allowed image mime type', function () {
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    foreach ($allowed as $mime => $extension) {
        expect(ImageExtension::fromUpload(fakeUploadWith($extension, $mime)))->toBe($extension);
    }

    expect(ImageExtension::allowed())->toBe(array_values($allowed));
});

it('rejects gif uploads', function () {
    expect(fn () => ImageExtension::fromUpload(fakeUploadWith('gif', 'image/gif'), 'cover'))
        ->toThrow(ValidationException::class);
});

it('relies on the mimes list to reject gif because the image rule allows it', function () {
    // Rule `image` bawaan Laravel masih mengizinkan gif, jadi gif harus
    // ditolak oleh daftar `mimes:` pada aturan validasi.
    $rules = (new StoreArticleRequest())->rules();

    $validator = Validator::make(
        ['cover' => fakeUploadWith('gif', 'image/gif')],
        ['cover' => $rules['cover']]
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('cover'))->toContain('must be a file of type');
});

it('rejects avif because the gd driver cannot decode it', function () {
    expect(fn () => ImageExtension::fromUpload(fakeUploadWith('avif', 'image/avif')))
        ->toThrow(ValidationException::class);
});

it('lists the allowed formats without avif in the error message', function () {
    try {
        ImageExtension::fromUpload(fakeUploadWith('avif', 'image/avif'), 'cover');
        $this->fail('Seharusnya melempar ValidationException.');
    } catch (ValidationException $e) {
        $message = $e->errors()['cover'][0] ?? '';

        expect($message)->toContain('image/avif')
            ->and($message)->toContain('jpg/jpeg, png, webp')
            ->and($message)->not->toContain('avif.');
    }
});

it('does not allow avif or gif in the api cover validation rule', function () {
    $rules = (new StoreArticleRequest())->rules();

    expect($rules['cover'])->not->toContain('avif');
    expect($rules['cover'])->not->toContain('gif');
    expect($rules['cover'])->toContain('mimes:jpeg,png,jpg,webp');
});

it('fails cover validation for an avif file instead of crashing later', function () {
    $validator = Validator::make(
        ['cover' => fakeUploadWith('avif', 'image/avif')],
        ['cover' => (new StoreArticleRequest())->rules()['cover']]
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('cover'))->toBeTrue();
});

it('still blocks avif in the helper when the mimes rule alone would allow it', function () {
    // Jebakan: aturan yang HANYA memakai `mimes:` (tanpa rule `image`)
    // akan meloloskan avif di lapisan validasi — terbukti di bawah.
    $validator = Validator::make(
        ['cover' => fakeUploadWith('avif', 'image/avif')],
        ['cover' => 'nullable|mimes:jpeg,png,jpg,gif,webp,avif']
    );

    expect($validator->passes())->toBeTrue();

    // Lapisan kedua (ImageExtension) tetap menolaknya, dan menolaknya sebagai
    // ValidationException — bukan exception mentah yang jadi error 500.
    expect(fn () => ImageExtension::fromUpload(fakeUploadWith('avif', 'image/avif'), 'cover'))
        ->toThrow(ValidationException::class);
});
