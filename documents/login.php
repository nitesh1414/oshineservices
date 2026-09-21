<?php
/**
 * Sign-in handler used by the "Admin" popup on the website (index.html).
 * On success the visitor is taken to the document dashboard; on failure back
 * to the website popup with an error flag.
 */
require __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$password = isset($_POST['password']) && !is_array($_POST['password']) ? (string) $_POST['password'] : '';

if (login_with_password($password)) {
    header('Location: ' . safe_next($_POST['next'] ?? ''));
    exit;
}

header('Location: ' . $settings['site_url'] . '?login=failed#popup');
exit;
