<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Normalisasi parameter query yang datang dari luar: kolom sort, arah, dan ukuran halaman.
 * Tujuannya sederhana — nilai tak dikenal memakai default (bukan 500), dan limit selalu dibatasi.
 */
final class QueryParams
{
    /**
     * Kolom sort yang diizinkan; kalau tidak ada di daftar, pakai default.
     */
    public static function sort(Request $request, array $allowed, string $default, string $param = 'sort'): string
    {
        $sort = $request->query($param, $default);

        return is_string($sort) && in_array($sort, $allowed, true) ? $sort : $default;
    }

    /**
     * Arah sort: hanya 'asc' atau 'desc'.
     */
    public static function direction(Request $request, string $default = 'desc', string $param = 'direction'): string
    {
        $direction = strtolower((string) $request->query($param, $default));

        return $direction === 'asc' ? 'asc' : 'desc';
    }

    /**
     * Jumlah item per halaman: dibatasi 1..$max (default kalau nilai tidak masuk akal).
     */
    public static function perPage(Request $request, int $default = 9, int $max = 100): int
    {
        $limit = $request->query('limit', $default);

        if (! is_numeric($limit)) {
            return $default;
        }

        return max(1, min((int) $limit, $max));
    }

    /**
     * Angka opsional (mis. limit koleksi): null kalau tidak dikirim, dibatasi 1..$max kalau dikirim.
     */
    public static function optionalLimit(Request $request, int $max = 100): ?int
    {
        $limit = $request->query('limit');

        if (! is_numeric($limit) || (int) $limit < 1) {
            return null;
        }

        return min((int) $limit, $max);
    }
}
