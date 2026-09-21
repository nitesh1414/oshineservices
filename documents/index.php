<?php
/**
 * Document generator dashboard.
 *  - Not signed in  → sign-in card
 *  - Signed in      → choose document type (Invoice / Quotation) and organization
 */
require __DIR__ . '/includes/bootstrap.php';

$next       = safe_next($_GET['next'] ?? ($_POST['next'] ?? ''));
$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = isset($_POST['password']) && !is_array($_POST['password']) ? (string) $_POST['password'] : '';
    if (login_with_password($password)) {
        header('Location: ' . $next);
        exit;
    }
    $loginError = 'Incorrect password. Please try again.';
}

$notice = '';
if (isset($_GET['error']) && $_GET['error'] === 'organization') {
    $notice = 'Please choose an organization first.';
}

if (!is_logged_in()) {
    echo render_template('layout/header', ['pageTitle' => 'Sign in', 'settings' => $settings, 'bodyClass' => 'is-login']);
    echo render_template('pages/login', ['settings' => $settings, 'loginError' => $loginError, 'next' => $next]);
    echo render_template('layout/footer', ['settings' => $settings]);
    exit;
}

echo render_template('layout/header', ['pageTitle' => 'Dashboard', 'settings' => $settings]);
echo render_template('pages/dashboard', ['settings' => $settings, 'organizations' => $organizations, 'notice' => $notice]);
echo render_template('layout/footer', ['settings' => $settings]);
