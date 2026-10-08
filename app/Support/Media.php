<?php

namespace App\Support;

use App\Exceptions\MediaException;
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
        return self::cloudinaryUrl() !== '';
    }

    /**
     * Keadaan konfigurasi penyimpanan, untuk ditampilkan ke admin tanpa membuka rahasia.
     *
     * @return array{state: 'cloudinary'|'invalid'|'local', detail: string}
     */
    public static function status(): array
    {
        $url = self::cloudinaryUrl();

        if ($url === '') {
            return ['state' => 'local', 'detail' => 'CLOUDINARY_URL kosong; gambar disimpan di disk server (tidak bisa di Vercel).'];
        }

        $parts = parse_url(trim($url));
        if (($parts['scheme'] ?? null) !== 'cloudinary' || empty($parts['user']) || empty($parts['pass']) || empty($parts['host'])) {
            return ['state' => 'invalid', 'detail' => 'CLOUDINARY_URL terbaca tapi formatnya salah. Harus cloudinary://API_KEY:API_SECRET@CLOUD_NAME.'];
        }

        return ['state' => 'cloudinary', 'detail' => 'Cloudinary, cloud "'.$parts['host'].'", API key berakhiran …'.substr($parts['user'], -4).'.'];
    }

    public static function store(UploadedFile $file, string $folder, bool $private = false): string
    {
        try {
            if (self::cloudinaryEnabled()) {
                $type = $private ? 'private' : 'upload';
                $result = self::client()->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'perpustakaan/'.$folder,
                    'type' => $type,
                    'resource_type' => 'image',
                ]);

                return "cloudinary:{$type}:{$result['public_id']}.{$result['format']}";
            }

            $path = $private ? $file->store($folder, 'local') : $file->store($folder, 'public');
            if ($path === false) {
                // Disk lokal tidak bisa ditulis, misalnya di Vercel tanpa CLOUDINARY_URL.
                throw new \RuntimeException('Disk lokal tidak bisa ditulis dan CLOUDINARY_URL kosong.');
            }

            return $private ? 'private:'.$path : $path;
        } catch (\Throwable $e) {
            report($e);

            throw new MediaException('Gambar gagal disimpan. Coba lagi sebentar lagi; kalau tetap gagal, hubungi petugas perpustakaan.', previous: $e);
        }
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

        // Menghapus file lama hanya beres-beres: kalau gagal, catat saja, jangan gagalkan request.
        try {
            if (Str::startsWith($key, 'cloudinary:')) {
                if (self::cloudinaryEnabled()) {
                    [, $type, $path] = explode(':', $key, 3);
                    self::client()->uploadApi()->destroy(Str::beforeLast($path, '.'), ['type' => $type]);
                }
            } elseif (Str::startsWith($key, 'private:')) {
                Storage::disk('local')->delete(Str::after($key, 'private:'));
            } else {
                Storage::disk('public')->delete($key);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Nilai CLOUDINARY_URL tanpa spasi dan tanpa awalan "CLOUDINARY_URL=" yang sering ikut tertempel. */
    private static function cloudinaryUrl(): string
    {
        $url = trim((string) config('services.cloudinary.url'), " \t\n\r\"'");

        return preg_replace('/^CLOUDINARY_URL\s*=\s*/i', '', $url);
    }

    private static function client(): Cloudinary
    {
        return new Cloudinary(self::cloudinaryUrl());
    }
}
