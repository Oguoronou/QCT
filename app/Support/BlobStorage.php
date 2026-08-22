<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Wraps the "s3" disk so uploaded files (item photos, profile pictures,
 * police declaration receipts, site logo) work the same whether that disk
 * is real AWS S3 or an S3-compatible provider (e.g. Cloudflare R2).
 *
 * Stored DB values are the full public URL, not a relative path: Laravel's
 * asset() helper returns an already-absolute URL unchanged, so every existing
 * Blade view calling asset($item->images) etc. keeps working without changes.
 */
class BlobStorage
{
    public static function store(UploadedFile $file, string $folder): string
    {
        $path = $file->store($folder, 's3');

        return Storage::disk('s3')->url($path);
    }

    public static function delete(?string $url): void
    {
        if (!$url) {
            return;
        }

        $key = self::keyFromUrl($url);

        if ($key) {
            Storage::disk('s3')->delete($key);
        }
    }

    private static function keyFromUrl(string $url): ?string
    {
        $base = rtrim(Storage::disk('s3')->url(''), '/');

        if ($base !== '' && Str::startsWith($url, $base)) {
            return ltrim(Str::after($url, $base), '/');
        }

        // Not an S3 URL (e.g. a pre-migration local path) — nothing to delete here.
        return null;
    }
}
