<?php
/**
 * Admin panel – change the admin password.
 *
 * The password lives in config/settings.php ('admin_password'). A successful
 * change rewrites that entry with a password_hash() string; the plain password
 * is never stored anywhere.
 */
require __DIR__ . '/includes/bootstrap.php';
require_login();

$raw = static function (string $key): string {
    $value = $_POST[$key] ?? '';
    return is_string($value) ? $value : '';
};

$errors        = [];
$changed       = isset($_GET['changed']);
$manualHash    = '';
$minLength     = 8;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $raw('current_password');
    $new     = $raw('new_password');
    $confirm = $raw('confirm_password');

    if (!csrf_verify($_POST['csrf'] ?? null)) {
        $errors[] = 'Your session has expired. Please try again.';
    } elseif (!verify_admin_password($current)) {
        register_failed_password_attempt();
        $errors[] = 'The current password is incorrect.';
    }

    if (strlen($new) < $minLength) {
        $errors[] = 'The new password must be at least ' . $minLength . ' characters long.';
    } elseif (strlen($new) > 200) {
        $errors[] = 'The new password is too long.';
    } elseif (trim($new) !== $new) {
        $errors[] = 'The new password must not start or end with spaces.';
    }
    if ($new !== $confirm) {
        $errors[] = 'The new password and its confirmation do not match.';
    }
    if (!$errors && $new === $current) {
        $errors[] = 'The new password must be different from the current one.';
    }

    if (!$errors) {
        $result = update_admin_password($new);
        if ($result['ok']) {
            session_regenerate_id(true);
            header('Location: password.php?changed=1');
            exit;
        }
        $errors[]   = $result['error'];
        $manualHash = $result['hash'];
    }
}

echo render_template('layout/header', ['pageTitle' => 'Change password', 'settings' => $settings]);
echo render_template('pages/password', [
    'settings'   => $settings,
    'errors'     => $errors,
    'changed'    => $changed,
    'manualHash' => $manualHash,
    'minLength'  => $minLength,
    'writable'   => settings_file_is_writable(),
    'isDefault'  => admin_password_is_default(),
    'isHashed'   => password_is_hashed((string) $settings['admin_password']),
]);
echo render_template('layout/footer', ['settings' => $settings, 'scripts' => ['assets/js/password.js?v=1']]);
