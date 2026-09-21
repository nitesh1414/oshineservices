<?php
/**
 * Page shell (top). Variables: $pageTitle, $settings, $org (optional), $bodyClass (optional)
 */
$org       = $org ?? null;
$accent    = $org['theme']['accent'] ?? '#FDA12B';
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= e($pageTitle) ?> · <?= e($settings['app_name']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link href="<?= e(asset_url('img/favicon.png')) ?>" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link href="<?= e(asset_url('css/bootstrap.min.css')) ?>" rel="stylesheet">
    <link href="assets/css/app.css?v=2" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#182333">
</head>
<body class="docs-body <?= e($bodyClass) ?>" style="--org-accent: <?= e($accent) ?>;">

<nav class="docs-nav">
    <div class="container-xxl d-flex align-items-center justify-content-between gap-3">
        <a class="docs-brand d-flex align-items-center gap-2" href="index.php">
            <img src="<?= e(asset_url('img/logo.png')) ?>" alt="Oshin Services" height="34">
            <span class="d-none d-sm-inline">Document Generator</span>
        </a>
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <?php if ($org): ?>
                <span class="org-chip" title="Issuing organization"><span class="dot"></span><?= e($org['name']) ?></span>
            <?php endif; ?>
            <?php if (is_logged_in()): ?>
                <a class="docs-link d-none d-md-inline" href="index.php">Dashboard</a>
                <a class="docs-link d-none d-md-inline" href="password.php">Change password</a>
                <a class="docs-link d-none d-md-inline" href="<?= e($settings['site_url']) ?>">Website</a>
                <a class="btn btn-sm btn-outline-light" href="logout.php">Sign out</a>
            <?php else: ?>
                <a class="docs-link" href="<?= e($settings['site_url']) ?>">&larr; Back to website</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="container-xxl docs-main">
