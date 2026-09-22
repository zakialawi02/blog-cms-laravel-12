<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Menggantikan pola lama (pesan exception langsung dipasang ke response publik):
 * detail exception masuk ke log, yang dikirim ke client hanya ID referensi pendek.
 */
final class ErrorReporter
{
    /** Catat detail lengkap ke log, kembalikan ID referensi pendek. */
    public static function reference(Throwable $e, string $context): string
    {
        $ref = substr(str_replace('-', '', (string) Str::uuid()), 0, 12);

        Log::error(sprintf('[%s] ref:%s %s: %s', $context, $ref, $e::class, $e->getMessage()), [
            'ref' => $ref,
            'context' => $context,
            'exception' => $e,
        ]);

        return $ref;
    }

    /** Sama seperti reference(), tapi mengembalikan string yang siap ditempel ke UI. */
    public static function refString(Throwable $e, string $context): string
    {
        return 'ref:' . self::reference($e, $context);
    }
}
