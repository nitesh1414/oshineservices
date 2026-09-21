<?php
/**
 * Ends the admin session and returns to the website.
 */
require __DIR__ . '/includes/bootstrap.php';
logout();
header('Location: ' . $settings['site_url']);
exit;
