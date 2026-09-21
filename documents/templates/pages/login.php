<?php
/**
 * Sign-in card. Variables: $settings, $loginError, $next
 */
?>
<div class="login-wrap">
    <div class="docs-card">
        <div class="docs-card-body">
            <div class="text-center mb-4">
                <img src="<?= e(asset_url('img/logo.png')) ?>" alt="Oshin Services" height="56">
                <h1 class="mt-3 mb-1">Admin Sign In</h1>
                <p class="text-muted text-small mb-0">Invoice &amp; Quotation Generator</p>
            </div>

            <?php if ($loginError !== ''): ?>
                <div class="alert alert-danger py-2 text-small" role="alert"><?= e($loginError) ?></div>
            <?php endif; ?>

            <form method="post" action="index.php">
                <input type="hidden" name="next" value="<?= e($next) ?>">
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" required autofocus autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Sign In</button>
            </form>

            <div class="text-center mt-4">
                <a class="text-small" href="<?= e($settings['site_url']) ?>">&larr; Back to website</a>
            </div>
        </div>
    </div>
</div>
