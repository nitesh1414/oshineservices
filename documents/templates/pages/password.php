<?php
/**
 * Change admin password page.
 * Variables: $settings, $errors, $changed, $manualHash, $minLength, $writable, $isDefault, $isHashed
 */
?>
<div class="page-head">
    <div>
        <div class="crumbs"><a href="index.php">Dashboard</a> &rsaquo; Change password</div>
        <h1>Change Admin Password</h1>
    </div>
    <a class="btn btn-outline-secondary btn-sm" href="index.php">&larr; Back to dashboard</a>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <section class="docs-card mb-0">
            <div class="docs-card-head">
                <h2><span class="step">&#128274;</span>New password</h2>
            </div>
            <div class="docs-card-body">
                <?php if ($changed): ?>
                    <div class="alert alert-success" role="alert">
                        <strong>Password updated.</strong> Use the new password the next time you sign in.
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            <?php foreach ($errors as $message): ?><li><?= e($message) ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!$writable): ?>
                    <div class="alert alert-warning text-small" role="alert">
                        <strong>Settings file is read-only.</strong> <code>documents/config/settings.php</code> is not writable by the web server,
                        so the password cannot be saved from here. Make the file writable (e.g. permissions <code>664</code> or <code>666</code> in your
                        hosting file manager) and reload this page, or edit the file manually.
                    </div>
                <?php endif; ?>

                <?php if ($manualHash !== ''): ?>
                    <div class="alert alert-info text-small" role="alert">
                        <strong>Manual update:</strong> replace the <code>admin_password</code> line in <code>documents/config/settings.php</code> with
                        <pre class="mb-0 mt-2 p-2 bg-white border" style="white-space:pre-wrap; word-break:break-all;">'admin_password' => '<?= e($manualHash) ?>',</pre>
                        <span class="text-muted">This is the secure hash of the password you just entered; the plain password is not shown or stored.</span>
                    </div>
                <?php endif; ?>

                <form method="post" action="password.php" id="passwordForm" class="needs-validation" novalidate autocomplete="off">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

                    <div class="mb-3">
                        <label class="form-label req" for="current_password">Current password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required autocomplete="current-password" autofocus>
                        <div class="invalid-feedback">Enter your current password.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label req" for="new_password">New password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="<?= (int) $minLength ?>" autocomplete="new-password">
                        <div class="form-text">At least <?= (int) $minLength ?> characters. Use a mix of letters, numbers and symbols.</div>
                        <div class="invalid-feedback">The new password must be at least <?= (int) $minLength ?> characters long.</div>
                        <div class="pw-meter mt-2" aria-hidden="true"><span id="pwMeterBar"></span></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label req" for="confirm_password">Confirm new password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="<?= (int) $minLength ?>" autocomplete="new-password">
                        <div class="invalid-feedback">Passwords do not match.</div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showPasswords">
                            <label class="form-check-label text-small" for="showPasswords">Show passwords</label>
                        </div>
                        <button type="submit" class="btn btn-primary px-4"<?= $writable ? '' : ' disabled' ?>>Update password</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <div class="col-lg-6">
        <section class="docs-card mb-3">
            <div class="docs-card-head">
                <h2><span class="step">i</span>Where it is stored</h2>
            </div>
            <div class="docs-card-body text-small">
                <ul class="status-list">
                    <li>
                        <span class="status-dot <?= $isDefault ? 'bad' : 'good' ?>"></span>
                        <?php if ($isDefault): ?>
                            <span><strong>Default password in use.</strong> The application still accepts the password it shipped with (<code><?= e(DEFAULT_ADMIN_PASSWORD) ?></code>). Change it now.</span>
                        <?php else: ?>
                            <span>A custom password is set.</span>
                        <?php endif; ?>
                    </li>
                    <li>
                        <span class="status-dot <?= $isHashed ? 'good' : 'warn' ?>"></span>
                        <?php if ($isHashed): ?>
                            <span>Stored as a secure hash (bcrypt) &mdash; the plain password is not kept anywhere.</span>
                        <?php else: ?>
                            <span>Currently stored as plain text in the config file. It will be replaced by a secure hash as soon as you change it here.</span>
                        <?php endif; ?>
                    </li>
                    <li>
                        <span class="status-dot <?= $writable ? 'good' : 'bad' ?>"></span>
                        <span><code>documents/config/settings.php</code> is <?= $writable ? 'writable &mdash; changes made here are saved directly to the file.' : '<strong>not writable</strong> &mdash; see the notice on the left.' ?></span>
                    </li>
                </ul>
                <hr>
                <p class="mb-1"><strong class="text-dark">Forgot the password?</strong></p>
                <p class="mb-0 text-muted">Open <code>documents/config/settings.php</code> on the server, set <code>'admin_password'</code> to a temporary plain-text value, sign in with it and then change it from this page.</p>
            </div>
        </section>
    </div>
</div>
