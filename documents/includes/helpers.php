<?php
/**
 * Small, dependency-free helper functions shared by the UI and the PDF templates.
 */

/**
 * HTML-escape a value for safe output.
 */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Escape a multi-line value and convert new lines to <br>.
 */
function nl2br_e($value): string
{
    return nl2br(e($value), false);
}

/**
 * URL of a shared website asset (the /css, /img folders of the main site).
 */
function asset_url(string $path): string
{
    return '../' . ltrim($path, '/');
}

/**
 * Fetch an organization profile by id, or null when it does not exist.
 */
function get_organization($id): ?array
{
    global $organizations;
    $id = (int) $id;
    return $organizations[$id] ?? null;
}

/**
 * Format a number using the Indian digit grouping (12,34,567.89).
 */
function format_inr($amount, int $decimals = 2): string
{
    $amount   = (float) $amount;
    $negative = $amount < 0;
    $amount   = abs($amount);

    $fixed    = number_format($amount, $decimals, '.', '');
    [$int, $frac] = array_pad(explode('.', $fixed, 2), 2, '');

    if (strlen($int) > 3) {
        $last3 = substr($int, -3);
        $rest  = substr($int, 0, -3);
        $rest  = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
        $int   = $rest . ',' . $last3;
    }

    $out = $int . ($decimals > 0 ? '.' . $frac : '');
    return ($negative ? '-' : '') . $out;
}

/**
 * Format an amount with the currency symbol, e.g. "₹ 1,23,456.00".
 */
function money($amount, ?string $symbol = null): string
{
    global $settings;
    $symbol = $symbol ?? $settings['currency_symbol'];
    return $symbol . ' ' . format_inr($amount);
}

/**
 * Convert an amount to words using the Indian numbering system.
 * Example: 1234567.5 => "Rupees Twelve Lakh Thirty Four Thousand Five Hundred Sixty Seven and Paise Fifty Only"
 */
function amount_in_words($amount, string $currency = 'Rupees', string $subunit = 'Paise'): string
{
    $amount = round((float) $amount, 2);
    $rupees = (int) floor($amount);
    $paise  = (int) round(($amount - $rupees) * 100);

    $words = $currency . ' ' . ($rupees === 0 ? 'Zero' : number_to_words_indian($rupees));
    if ($paise > 0) {
        $words .= ' and ' . $subunit . ' ' . number_to_words_indian($paise);
    }
    return $words . ' Only';
}

/**
 * Integer to words (Indian system: Thousand, Lakh, Crore).
 */
function number_to_words_indian(int $number): string
{
    static $ones = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen',
    ];
    static $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    if ($number < 0) {
        return 'Minus ' . number_to_words_indian(-$number);
    }
    if ($number < 20) {
        return $ones[$number];
    }
    if ($number < 100) {
        return trim($tens[intdiv($number, 10)] . ' ' . $ones[$number % 10]);
    }
    if ($number < 1000) {
        return trim($ones[intdiv($number, 100)] . ' Hundred ' . number_to_words_indian($number % 100));
    }

    $parts = [];
    $units = [10000000 => 'Crore', 100000 => 'Lakh', 1000 => 'Thousand'];
    foreach ($units as $value => $label) {
        if ($number >= $value) {
            $parts[] = number_to_words_indian(intdiv($number, $value)) . ' ' . $label;
            $number  = $number % $value;
        }
    }
    if ($number > 0) {
        $parts[] = number_to_words_indian($number);
    }
    return implode(' ', $parts);
}

/**
 * Format an ISO date (Y-m-d) for display, e.g. 21-Sep-2026. Falls back to the raw value.
 */
function format_date(?string $isoDate, string $format = 'd-M-Y'): string
{
    if (!$isoDate) {
        return '';
    }
    $dt = DateTime::createFromFormat('Y-m-d', $isoDate);
    return $dt ? $dt->format($format) : $isoDate;
}

/**
 * Turn an arbitrary string into a safe file name component.
 */
function safe_filename(string $value): string
{
    $value = preg_replace('/[^A-Za-z0-9._-]+/', '-', $value);
    $value = trim(preg_replace('/-+/', '-', $value), '-.');
    return $value === '' ? 'document' : $value;
}

/**
 * Read a local image file and return it as a data URI (so PDFs never need network or file-system access).
 */
function image_data_uri(string $relativePath): string
{
    $path = APP_ROOT . '/' . ltrim($relativePath, '/');
    if (!is_file($path) || !is_readable($path)) {
        return '';
    }
    $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $mime = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'svg' => 'image/svg+xml'][$ext] ?? 'application/octet-stream';
    return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
}

/**
 * Render a PHP template file to a string.
 */
function render_template(string $template, array $vars = []): string
{
    $__file = APP_ROOT . '/templates/' . $template . '.php';
    if (!is_file($__file)) {
        throw new RuntimeException('Template not found: ' . $template);
    }
    extract($vars, EXTR_SKIP);
    ob_start();
    include $__file;
    return (string) ob_get_clean();
}

// ---------------------------------------------------------------------------
// Authentication helpers (session flag only)
// ---------------------------------------------------------------------------

function is_logged_in(): bool
{
    return !empty($_SESSION['osdocs_admin']);
}

/**
 * True when the configured value is a password_hash() string rather than plain text.
 */
function password_is_hashed(string $value): bool
{
    return (bool) preg_match('/^\$(2y|2a|2b|argon2id?)\$/', $value);
}

/**
 * Check a password against the configured admin password (config/settings.php).
 * Plain-text and password_hash() values are both supported.
 */
function verify_admin_password(string $password): bool
{
    global $settings;
    $expected = (string) ($settings['admin_password'] ?? '');

    if ($expected === '' || $password === '') {
        return false;
    }
    if (password_is_hashed($expected)) {
        return password_verify($password, $expected);
    }
    return hash_equals($expected, $password);
}

/**
 * True while the password shipped with the application is still in use.
 */
function admin_password_is_default(): bool
{
    return verify_admin_password(DEFAULT_ADMIN_PASSWORD);
}

/**
 * Record a failed password attempt and slow the response down (brute-force protection).
 */
function register_failed_password_attempt(): void
{
    $_SESSION['osdocs_failed'] = ($_SESSION['osdocs_failed'] ?? 0) + 1;
    usleep(min(5, (int) $_SESSION['osdocs_failed']) * 300000);
}

function login_with_password(string $password): bool
{
    $ok = verify_admin_password($password);

    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['osdocs_admin']    = true;
        $_SESSION['osdocs_login_at'] = time();
        unset($_SESSION['osdocs_failed']);
    } else {
        register_failed_password_attempt();
    }
    return $ok;
}

// ---------------------------------------------------------------------------
// Admin password management (stored in config/settings.php, never plain text
// once changed from the admin panel)
// ---------------------------------------------------------------------------

function settings_file(): string
{
    return APP_ROOT . '/config/settings.php';
}

function settings_file_is_writable(): bool
{
    $file = settings_file();
    return is_file($file) && is_writable($file);
}

/**
 * Replace the 'admin_password' entry in config/settings.php with a password_hash()
 * of the new password. The file is rewritten atomically (temporary file + rename)
 * whenever the directory allows it, and the result is verified before use.
 *
 * @return array{ok: bool, error: string, hash: string}
 */
function update_admin_password(string $newPassword): array
{
    global $settings;

    $file   = settings_file();
    $hash   = password_hash($newPassword, PASSWORD_DEFAULT);
    $result = ['ok' => false, 'error' => '', 'hash' => $hash];

    if (!is_file($file) || !is_readable($file)) {
        $result['error'] = 'The settings file (documents/config/settings.php) could not be read.';
        return $result;
    }
    if (!is_writable($file)) {
        $result['error'] = 'The settings file (documents/config/settings.php) is not writable by the web server, so the password could not be saved.';
        return $result;
    }

    $source  = (string) file_get_contents($file);
    $count   = 0;
    $updated = preg_replace_callback(
        "/('admin_password'\\s*=>\\s*)(?:'(?:[^'\\\\]|\\\\.)*'|\"(?:[^\"\\\\]|\\\\.)*\")/",
        static function (array $m) use ($hash): string {
            return $m[1] . "'" . $hash . "'";
        },
        $source,
        1,
        $count
    );

    if ($updated === null || $count !== 1) {
        $result['error'] = "The 'admin_password' entry could not be located in documents/config/settings.php.";
        return $result;
    }

    // Syntax guard (the replacement only swaps one quoted string, but never take chances with a config file).
    if (function_exists('token_get_all')) {
        try {
            token_get_all($updated, TOKEN_PARSE);
        } catch (ParseError $e) {
            $result['error'] = 'The updated settings file would not be valid PHP; nothing was changed.';
            return $result;
        }
    }

    $dir     = dirname($file);
    $written = false;

    if (is_writable($dir)) {
        // Atomic replace: write next to the original, verify, then rename over it.
        $tmp = $dir . '/.settings-' . bin2hex(random_bytes(6)) . '.tmp.php';
        if (file_put_contents($tmp, $updated, LOCK_EX) !== false) {
            $check = (static function (string $path) {
                try {
                    return include $path;
                } catch (Throwable $e) {
                    return null;
                }
            })($tmp);

            if (is_array($check) && ($check['admin_password'] ?? null) === $hash) {
                @chmod($tmp, fileperms($file) & 0777);
                $written = @rename($tmp, $file);
            }
            if (!$written) {
                @unlink($tmp);
            }
        }
    }

    if (!$written) {
        // Directory not writable (or rename refused): rewrite the file in place.
        $written = file_put_contents($file, $updated, LOCK_EX) !== false;
    }

    if (!$written) {
        $result['error'] = 'The settings file could not be written. Check the file permissions of documents/config/settings.php.';
        return $result;
    }

    if (function_exists('opcache_invalidate')) {
        @opcache_invalidate($file, true);
    }
    clearstatcache(true, $file);

    $settings['admin_password'] = $hash;
    $result['ok'] = true;
    return $result;
}

// ---------------------------------------------------------------------------
// CSRF protection for state-changing forms
// ---------------------------------------------------------------------------

function csrf_token(): string
{
    if (empty($_SESSION['osdocs_csrf'])) {
        $_SESSION['osdocs_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['osdocs_csrf'];
}

function csrf_verify($token): bool
{
    return is_string($token)
        && !empty($_SESSION['osdocs_csrf'])
        && hash_equals($_SESSION['osdocs_csrf'], $token);
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'domain'   => $p['domain'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => $p['samesite'] ?? 'Lax',
        ]);
    }
    session_destroy();
}

/**
 * Redirect to the sign-in page when the visitor is not authenticated.
 */
function require_login(): void
{
    if (!is_logged_in()) {
        $next = basename($_SERVER['SCRIPT_NAME']) . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
        header('Location: index.php?next=' . rawurlencode($next));
        exit;
    }
}

/**
 * Only allow redirects to pages inside this application.
 */
function safe_next(?string $next): string
{
    $next = (string) $next;
    if ($next === '' || !preg_match('#^(?:(?:create_invoice|create_quote)\.php(?:\?id=\d+)?|password\.php)$#', $next)) {
        return 'index.php';
    }
    return $next;
}

/**
 * Read a value from a request array and normalise it to a trimmed string.
 */
function post_string(array $source, string $key, string $default = ''): string
{
    if (!isset($source[$key]) || is_array($source[$key])) {
        return $default;
    }
    $value = trim((string) $source[$key]);
    // Strip control characters except tab/newline.
    return preg_replace('/[^\P{C}\t\n]+/u', '', $value) ?? '';
}
