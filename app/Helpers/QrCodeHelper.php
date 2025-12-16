<?php

namespace App\Helpers;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeHelper
{
    /**
     * Genera un código QR en formato SVG base64.
     *
     * @param string $content El contenido del QR.
     * @param int $size El tamaño del QR en pixeles.
     * @return string La cadena data URI base64 del SVG.
     */
    public static function generateSvgBase64(string $content, int $size = 200): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($content);

        // Remove XML declaration if present to avoid issues in some renderers/browsers when used as data URI
        $svg = preg_replace('/<\?xml.*?\?>/', '', $svg);

        return 'data:image/svg+xml;base64,' . base64_encode(trim($svg));
    }

    /**
     * Genera un código QR usando una API externa (fallback si falla la generación local).
     * Retorna una imagen PNG en Base64.
     *
     * @param string $content
     * @param int $size
     * @return string
     */
    public static function generateFromApi(string $content, int $size = 150): string
    {
        try {
            $url = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($content);
            $imageContent = file_get_contents($url);

            if ($imageContent === false) {
                return ''; // O manejar error de otra forma
            }

            // Guardamos la imagen en una carpeta pública temporal para asegurar acceso
            $filename = 'qr_' . md5($content) . '.png';
            $dir = public_path('qrs_temp');

            // Asegurar que el directorio existe
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $path = $dir . DIRECTORY_SEPARATOR . $filename;
            file_put_contents($path, $imageContent);

            // Retornamos la ruta absoluta del archivo
            return $path;
        } catch (\Exception $e) {
            // Log::error("Error generando QR desde API: " . $e->getMessage());
            return '';
        }
    }
}
