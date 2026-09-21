<?php
/**
 * Dashboard: pick a document type and an organization.
 * Variables: $settings, $organizations, $notice
 */
$types = [
    ['label' => 'Invoice Generator',   'script' => 'create_invoice.php', 'blurb' => 'GST tax invoice with HSN/SAC summary, bank details and amount in words.', 'icon' => '&#8377;'],
    ['label' => 'Quotation Generator', 'script' => 'create_quote.php',   'blurb' => 'Letter-style quotation with subject, validity, line items and terms.',     'icon' => '&#9998;'],
];
?>
<div class="page-head">
    <div>
        <div class="crumbs">Dashboard</div>
        <h1>What would you like to create?</h1>
    </div>
    <span class="text-small text-muted">Select the document type, then the issuing organization.</span>
</div>

<?php if (!empty($notice)): ?>
    <div class="alert alert-warning" role="alert"><?= e($notice) ?></div>
<?php endif; ?>

<?php if (admin_password_is_default()): ?>
    <div class="alert alert-danger alert-default-password d-flex flex-wrap align-items-center justify-content-between gap-2" role="alert">
        <span><strong>Security notice:</strong> the admin panel still uses the default password. Please set your own password before using the generator.</span>
        <a class="btn btn-danger btn-sm" href="password.php">Change password now</a>
    </div>
<?php endif; ?>

<?php foreach ($types as $type): ?>
    <section class="type-section">
        <h2><span class="ico"><?= $type['icon'] ?></span><?= e($type['label']) ?></h2>
        <p class="text-small text-muted mb-3"><?= e($type['blurb']) ?></p>
        <div class="row g-3">
            <?php foreach ($organizations as $id => $org): ?>
                <div class="col-md-6">
                    <a class="org-card" href="<?= e($type['script']) ?>?id=<?= (int) $id ?>" style="--card-accent: <?= e($org['theme']['accent']) ?>;">
                        <span class="logo"><?php if (!empty($org['logo'])): ?><img src="<?= e($org['logo']) ?>" alt=""><?php endif; ?></span>
                        <span>
                            <h3><?= e($org['name']) ?></h3>
                            <p>GSTIN <?= e($org['gstin']) ?><?= (string) $org['vendor_code'] !== '' ? ' · BPCL Vendor Code ' . e($org['vendor_code']) : '' ?></p>
                        </span>
                        <span class="go">Create &rarr;</span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>

<div class="docs-card">
    <div class="docs-card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="text-small">
            <strong class="text-dark">Admin password</strong><br>
            <span class="text-muted">Stored in <code>documents/config/settings.php</code> as a secure hash. Change it whenever needed.</span>
        </div>
        <a class="btn btn-outline-secondary btn-sm" href="password.php">Change password</a>
    </div>
</div>

<div class="docs-card">
    <div class="docs-card-body text-small text-muted">
        <strong class="text-dark">How it works:</strong> fill in the document details, add line items and click <em>Download PDF</em>.
        The document is rendered on the server and immediately downloaded to your computer &mdash; no invoice or quotation data is stored anywhere.
        Keep your own record of issued numbers.
    </div>
</div>
