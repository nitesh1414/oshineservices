<?php
/**
 * Application bootstrap: configuration, sessions, autoloading and error handling.
 * Included at the top of every public entry point.
 */

define('APP_ROOT', dirname(__DIR__));

$settings      = require APP_ROOT . '/config/settings.php';
$organizations = require APP_ROOT . '/config/organizations.php';

require_once APP_ROOT . '/includes/helpers.php';
require_once APP_ROOT . '/includes/Document.php';

// ---------------------------------------------------------------------------
// Error handling: never leak stack traces to visitors, always log them.
// ---------------------------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function (Throwable $e) use ($settings) {
    error_log(sprintf('[documents] %s in %s:%d', $e->getMessage(), $e->getFile(), $e->getLine()));
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }
    $appName = e($settings['app_name']);
    $hint    = '';
    if (stripos($e->getMessage(), 'dompdf') !== false || stripos($e->getMessage(), 'autoload') !== false) {
        $hint = '<p class="mb-0 small">The PDF library could not be loaded. Make sure the <code>documents/vendor</code> folder was uploaded completely.</p>';
    }
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>Error · ' . $appName . '</title>'
        . '<meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<link href="' . e(asset_url('css/bootstrap.min.css')) . '" rel="stylesheet"></head>'
        . '<body class="bg-light"><div class="container py-5" style="max-width:640px">'
        . '<div class="card border-0 shadow-sm"><div class="card-body p-4">'
        . '<h1 class="h4 mb-3">Something went wrong</h1>'
        . '<p>The document could not be generated. Please go back and try again. If the problem persists, contact the administrator.</p>'
        . $hint
        . '<a class="btn btn-primary mt-3" href="javascript:history.back()">Go back</a>'
        . '</div></div></div></body></html>';
    exit;
});

// ---------------------------------------------------------------------------
// Session (only used for the admin sign-in flag – no document data is stored)
// ---------------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_name($settings['session_name']);
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

date_default_timezone_set('Asia/Kolkata');
mb_internal_encoding('UTF-8');
