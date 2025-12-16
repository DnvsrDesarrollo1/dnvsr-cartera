<?php
require __DIR__ . '/vendor/autoload.php';

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

try {
    $renderer = new ImageRenderer(
        new RendererStyle(200),
        new SvgImageBackEnd()
    );
    $writer = new Writer($renderer);
    $svg = $writer->writeString('TestContent');

    echo "--- START SVG ---\n";
    echo $svg;
    echo "\n--- END SVG ---\n";

    echo "\n--- START BASE64 ---\n";
    echo 'data:image/svg+xml;base64,' . base64_encode($svg);
    echo "\n--- END BASE64 ---\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage();
}
