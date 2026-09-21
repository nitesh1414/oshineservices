<?php
/**
 * PDF rendering (dompdf). The PDF is generated in memory and streamed straight
 * to the browser – it is never written to the server.
 */

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Render an HTML document to PDF and send it to the browser.
 *
 * @param string $html      Complete HTML document
 * @param string $filename  Suggested file name (e.g. Invoice-001.pdf)
 * @param bool   $download  true = "Save as" download, false = open inline in the browser
 * @param array  $meta      PDF metadata: title, author, subject, keywords
 */
function stream_pdf(string $html, string $filename, bool $download = true, array $meta = []): void
{
    $autoload = APP_ROOT . '/vendor/autoload.php';
    if (!is_file($autoload)) {
        throw new RuntimeException('dompdf autoloader not found – the vendor directory is missing.');
    }
    require_once $autoload;

    $options = new Options();
    $options->setIsRemoteEnabled(false);        // images are embedded as data URIs, no network access needed
    $options->setIsPhpEnabled(false);
    $options->setIsHtml5ParserEnabled(true);
    $options->setIsFontSubsettingEnabled(true); // keeps files small
    $options->setDefaultFont('DejaVu Sans');    // bundled font with the ₹ glyph
    $options->setDefaultPaperSize('A4');
    $options->setDpi(96);
    $options->setChroot(APP_ROOT);

    // Use a writable cache/temp folder when available (shared hosts often mount vendor read-only).
    $storage = APP_ROOT . '/storage';
    if (!is_dir($storage)) {
        @mkdir($storage, 0775, true);
    }
    if (is_dir($storage) && is_writable($storage)) {
        foreach (['fontcache', 'tmp'] as $dir) {
            if (!is_dir($storage . '/' . $dir)) {
                @mkdir($storage . '/' . $dir, 0775, true);
            }
        }
        if (is_writable($storage . '/tmp')) {
            $options->setTempDir($storage . '/tmp');
        }
        if (is_writable($storage . '/fontcache')) {
            $options->setFontCache($storage . '/fontcache');
        }
    }

    $dompdf = new Dompdf($options);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->render();

    // Page footer: page numbers on every page.
    $canvas = $dompdf->getCanvas();
    $font   = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
    $w      = $canvas->get_width();
    $h      = $canvas->get_height();
    $footer = ($meta['footer'] ?? '');
    if ($footer !== '') {
        $canvas->page_text(36, $h - 28, $footer, $font, 7, [0.45, 0.45, 0.45]);
    }
    $canvas->page_text($w - 100, $h - 28, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 7, [0.45, 0.45, 0.45]);

    foreach (['Title', 'Author', 'Subject', 'Keywords'] as $key) {
        if (!empty($meta[strtolower($key)])) {
            $dompdf->addInfo($key, (string) $meta[strtolower($key)]);
        }
    }
    $dompdf->addInfo('Creator', 'Oshin Services Document Generator');

    $output = $dompdf->output();

    // Discard any stray output (warnings, whitespace) so the PDF stream stays valid.
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    $safeName = safe_filename(pathinfo($filename, PATHINFO_FILENAME)) . '.pdf';

    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($output));
    header('Content-Disposition: ' . ($download ? 'attachment' : 'inline') . '; filename="' . $safeName . '"; filename*=UTF-8\'\'' . rawurlencode($safeName));
    header('Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('X-Content-Type-Options: nosniff');
    echo $output;
    exit;
}
