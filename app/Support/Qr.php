<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/** QR sebagai SVG murni: tidak butuh ekstensi GD, jadi jalan juga di runtime Vercel. */
class Qr
{
    public static function svg(string $text, int $size = 160): string
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd));

        // Buang deklarasi XML supaya bisa disisipkan langsung ke HTML.
        return trim(preg_replace('/^<\?xml[^>]*\?>/', '', $writer->writeString($text)));
    }

    /** Untuk dompdf, yang lebih andal membaca SVG lewat <img src="data:...">. */
    public static function dataUri(string $text, int $size = 160): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode(self::svg($text, $size));
    }
}
