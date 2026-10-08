<?php

namespace App\Support;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Penyimpanan gambar untuk cover, foto profil, usulan buku, dan KTM.
 *
 * Kalau CLOUDINARY_URL terisi, file dikirim ke Cloudinary (filesystem Vercel read-only,
 * jadi upload tidak bisa disimpan di server). Kalau kosong, dipakai disk lokal Laravel
 * supaya pengembangan tidak butuh akun apa pun.
 *
 * Yang disimpan di database adalah "kunci":
 *   cloudinary:upload:perpustakaan/covers/abc.jpg   gambar publik di Cloudinary
 *   cloudinary:private:perpustakaan/ktm/def.png     gambar privat di Cloudinary
 *   covers/abc.jpg                                  disk "public" lokal
 *   private:ktm/def.png                             disk "local" (tidak bisa diakses publik)
 *   https://...                                     URL luar, misalnya cover Open Library
 */
class Media
{
    private const PRIVATE_URL_TTL = 300;

    public static function cloudinaryEnabled(): bool
    {
        return filled(config('services.cloudinary.url'));
    }

    public static function store(UploadedFile $file, string $folder, bool $private = false): string
    {
        if (self::cloudinaryEnabled()) {
            $type = $private ? 'private' : 'upload';
            $result = self::client()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'perpustakaan/'.$folder,
                'type' => $type,
                'resource_type' => 'image',
            ]);

            return "cloudinary:{$type}:{$result['public_id']}.{$result['format']}";
        }

        if ($private) {
            return 'private:'.$file->store($folder, 'local');
        }

        return $file->store($folder, 'public');
    }

    /**
     * URL untuk ditampilkan. Untuk gambar privat, URL-nya bertanda tangan dan
     * kedaluwarsa, jadi hanya boleh dipanggil dari halaman yang sudah dicek haknya.
     */
    public static function url(?string $key, ?int $width = null): ?string
    {
        if (blank($key)) {
            return null;
        }

        if (Str::startsWith($key, ['http://', 'https://'])) {
            return $key;
        }

        if (Str::startsWith($key, 'cloudinary:')) {
            [, $type, $path] = explode(':', $key, 3);
            $publicId = Str::beforeLast($path, '.');
            $format = Str::afterLast($path, '.');

            if ($type === 'private') {
                return self::client()->uploadApi()->privateDownloadUrl($publicId, $format, [
                    'type' => 'private',
                    'expires_at' => time() + self::PRIVATE_URL_TTL,
                ]);
            }

            $image = self::client()->image($publicId)->format($format);
            if ($width) {
                $image->resize(\Cloudinary\Transformation\Resize::limitFit($width));
            }

            return (string) $image->toUrl();
        }

        if (Str::startsWith($key, 'private:')) {
            // Disk lokal privat hanya disajikan lewat rute yang dijaga middleware admin.
            return null;
        }

        return Storage::disk('public')->url($key);
    }

    public static function localPrivatePath(string $key): ?string
    {
        if (! Str::startsWith($key, 'private:')) {
            return null;
        }

        return Storage::disk('local')->path(Str::after($key, 'private:'));
    }

    public static function delete(?string $key): void
    {
        if (blank($key) || Str::startsWith($key, ['http://', 'https://'])) {
            return;
        }

        if (Str::startsWith($key, 'cloudinary:')) {
            if (! self::cloudinaryEnabled()) {
                return;
            }
            [, $type, $path] = explode(':', $key, 3);
            self::client()->uploadApi()->destroy(Str::beforeLast($path, '.'), ['type' => $type]);

            return;
        }

        if (Str::startsWith($key, 'private:')) {
            Storage::disk('local')->delete(Str::after($key, 'private:'));

            return;
        }

        Storage::disk('public')->delete($key);
    }

    private static function client(): Cloudinary
    {
        return new Cloudinary(config('services.cloudinary.url'));
    }
}
