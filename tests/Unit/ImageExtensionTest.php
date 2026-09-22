<?php

use App\Support\ImageExtension;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

uses(Tests\TestCase::class);

it('menolak file non-gambar walau bernama .php', function () {
    $f = UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;');

    expect(fn () => ImageExtension::fromUpload($f, 'upload'))
        ->toThrow(ValidationException::class);
});

it('mengambil ekstensi dari MIME, bukan dari nama kiriman client', function () {
    // PNG asli dibuat dengan GD supaya MIME hasil deteksi konten pasti image/png
    $tmp = tempnam(sys_get_temp_dir(), 'zk') . '.png';
    imagepng(imagecreatetruecolor(2, 2), $tmp);

    // nama kiriman client sengaja berekstensi .php — inilah pola serangan yang ditutup
    $f = new UploadedFile($tmp, 'x.png.php', 'image/png', null, true);

    expect(ImageExtension::fromUpload($f, 'upload'))->toBe('png');
});

it('menolak svg', function () {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>';
    $f = UploadedFile::fake()->createWithContent('logo.svg', $svg);

    expect(fn () => ImageExtension::fromUpload($f, 'upload'))
        ->toThrow(ValidationException::class);
});
