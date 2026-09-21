<?php
/**
 * Document entry form (Invoice + Quotation).
 * Variables: $doc (Document), $org, $settings, $errors (array), $formAction
 */
$isInvoice = $doc->isInvoice();
$label     = $doc->typeLabel();
$errors    = $errors ?? [];
$c         = $doc->customer;
$igst      = $doc->tax_type === Document::TAX_IGST;

$err = static function (string $key) use ($errors): string {
    return isset($errors[$key]) ? ' is-invalid' : '';
};
$msg = static function (string $key) use ($errors): string {
    return isset($errors[$key]) ? '<div class="invalid-feedback">' . e($errors[$key]) . '</div>' : '';
};
$itemErrors = [];
foreach ($errors as $key => $message) {
    if (strpos($key, 'items.') === 0) {
        $itemErrors[] = $message;
    }
}
?>
<div class="page-head">
    <div>
        <div class="crumbs"><a href="index.php">Dashboard</a> &rsaquo; <?= e($label) ?> &rsaquo; <?= e($org['name']) ?></div>
        <h1>New <?= e($label) ?></h1>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="index.php">Change organization</a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger" role="alert">
        <strong>Please correct the following before generating the PDF:</strong>
        <ul class="mb-0 mt-1">
            <?php foreach ($errors as $message): ?>
                <li><?= e($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= e($formAction) ?>" id="documentForm" class="needs-validation" novalidate
      data-tax-type="<?= e($doc->tax_type) ?>" data-currency="<?= e($settings['currency_symbol']) ?>"
      data-default-gst="<?= e((string) $settings['default_gst_rate']) ?>" data-default-unit="<?= e($settings['default_unit']) ?>">

    <!-- 1. Organisation + document details -->
    <section class="docs-card">
        <div class="docs-card-head">
            <h2><span class="step">1</span><?= e($label) ?> Details</h2>
            <span class="text-small text-muted">Issued by <?= e($org['name']) ?></span>
        </div>
        <div class="docs-card-body">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="org-identity">
                        <div class="org-logo">
                            <?php if (!empty($org['logo'])): ?><img src="<?= e($org['logo']) ?>" alt="<?= e($org['name']) ?>"><?php endif; ?>
                        </div>
                        <div>
                            <h3><?= e($org['name']) ?></h3>
                            <?php if (!empty($org['tagline'])): ?><div class="tagline"><?= e($org['tagline']) ?></div><?php endif; ?>
                            <div class="meta">
                                <?= e($org['address']) ?><br>
                                <b>Phone:</b> <?= e($org['phone']) ?> &nbsp;·&nbsp; <b>Email:</b> <?= e($org['email']) ?><br>
                                <b>GSTIN:</b> <?= e($org['gstin']) ?> &nbsp;·&nbsp; <b>State:</b> <?= e($org['state']) ?> (<?= e($org['state_code']) ?>)
                                <?php if ((string) $org['vendor_code'] !== ''): ?><br><b>BPCL Vendor Code:</b> <?= e($org['vendor_code']) ?><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label req" for="number"><?= e($label) ?> No.</label>
                            <input type="text" class="form-control<?= $err('number') ?>" id="number" name="number" value="<?= e($doc->number) ?>" maxlength="40" required autocomplete="off" autofocus>
                            <?= $msg('number') ?: '<div class="invalid-feedback">' . e($label) . ' number is required.</div>' ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label req" for="date"><?= e($label) ?> Date</label>
                            <input type="date" class="form-control<?= $err('date') ?>" id="date" name="date" value="<?= e($doc->date) ?>" required>
                            <?= $msg('date') ?: '<div class="invalid-feedback">Please enter a valid date.</div>' ?>
                        </div>
                        <?php if ($isInvoice): ?>
                            <div class="col-sm-6">
                                <label class="form-label" for="po_number">PO / Work Order No.</label>
                                <input type="text" class="form-control" id="po_number" name="po_number" value="<?= e($doc->po_number) ?>" maxlength="60" autocomplete="off">
                            </div>
                        <?php else: ?>
                            <div class="col-sm-6">
                                <label class="form-label" for="valid_until">Valid Until</label>
                                <input type="date" class="form-control<?= $err('valid_until') ?>" id="valid_until" name="valid_until" value="<?= e($doc->valid_until) ?>">
                                <?= $msg('valid_until') ?>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="reference">Reference / Enquiry No.</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="<?= e($doc->reference) ?>" maxlength="120" autocomplete="off">
                            </div>
                        <?php endif; ?>
                        <div class="col-sm-6">
                            <label class="form-label" for="tax_type">Tax Type</label>
                            <select class="form-select" id="tax_type" name="tax_type">
                                <option value="cgst_sgst"<?= !$igst ? ' selected' : '' ?>>CGST + SGST (within state)</option>
                                <option value="igst"<?= $igst ? ' selected' : '' ?>>IGST (inter-state)</option>
                            </select>
                        </div>
                        <?php if (!$isInvoice): ?>
                            <div class="col-12">
                                <label class="form-label req" for="subject">Subject</label>
                                <input type="text" class="form-control<?= $err('subject') ?>" id="subject" name="subject" value="<?= e($doc->subject) ?>" maxlength="200" required placeholder="e.g. Quotation for painting work at BPCL retail outlet, Wardha Road">
                                <?= $msg('subject') ?: '<div class="invalid-feedback">Subject is required.</div>' ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Customer -->
    <section class="docs-card">
        <div class="docs-card-head">
            <h2><span class="step">2</span><?= $isInvoice ? 'Bill To' : 'Quotation To' ?></h2>
            <span class="text-small text-muted">Pre-filled with the default customer &mdash; edit as needed</span>
        </div>
        <div class="docs-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="customer_attention">Attention / Designation</label>
                    <input type="text" class="form-control" id="customer_attention" name="customer[attention]" value="<?= e($c['attention']) ?>" maxlength="120" placeholder="e.g. The Territory Manager (RS)">
                </div>
                <div class="col-md-8">
                    <label class="form-label req" for="customer_name">Customer / Company Name</label>
                    <input type="text" class="form-control<?= $err('customer.name') ?>" id="customer_name" name="customer[name]" value="<?= e($c['name']) ?>" maxlength="120" required>
                    <?= $msg('customer.name') ?: '<div class="invalid-feedback">Customer name is required.</div>' ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="customer_address">Address</label>
                    <textarea class="form-control" id="customer_address" name="customer[address]" rows="3" maxlength="500"><?= e($c['address']) ?></textarea>
                </div>
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label" for="customer_gstin">GSTIN</label>
                            <input type="text" class="form-control text-uppercase<?= $err('customer.gstin') ?>" id="customer_gstin" name="customer[gstin]" value="<?= e($c['gstin']) ?>" maxlength="15" pattern="[0-9]{2}[A-Za-z0-9]{13}" autocomplete="off">
                            <?= $msg('customer.gstin') ?: '<div class="invalid-feedback">GSTIN must be 15 characters.</div>' ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="customer_state_code">State Code</label>
                            <input type="text" class="form-control" id="customer_state_code" name="customer[state_code]" value="<?= e($c['state_code']) ?>" maxlength="2" inputmode="numeric" pattern="[0-9]{1,2}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="customer_place_of_supply">Place of Supply</label>
                            <input type="text" class="form-control" id="customer_place_of_supply" name="customer[place_of_supply]" value="<?= e($c['place_of_supply']) ?>" maxlength="120">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Line items -->
    <section class="docs-card">
        <div class="docs-card-head">
            <h2><span class="step">3</span>Line Items</h2>
            <span class="text-small text-muted">Amounts are calculated automatically; GST is applied per line</span>
        </div>
        <?php if ($itemErrors): ?>
            <div class="alert alert-warning rounded-0 mb-0 border-0 border-bottom">
                <?= implode('<br>', array_map('e', $itemErrors)) ?>
            </div>
        <?php endif; ?>
        <div class="items-wrap">
            <table class="table items-table" id="itemsTable">
                <thead>
                    <tr>
                        <th class="sr">#</th>
                        <th style="min-width:280px">Item Description <span class="text-danger">*</span></th>
                        <th style="width:110px">HSN/SAC</th>
                        <th style="width:100px">SOR No.</th>
                        <th style="width:90px" class="num">Qty <span class="text-danger">*</span></th>
                        <th style="width:100px">Unit</th>
                        <th style="width:120px" class="num">Rate (<?= e($settings['currency_symbol']) ?>) <span class="text-danger">*</span></th>
                        <th style="width:90px">GST %</th>
                        <th style="width:120px" class="num">Taxable</th>
                        <th style="width:110px" class="num">GST Amt</th>
                        <th style="width:130px" class="num">Line Total</th>
                        <th style="width:44px"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <?php foreach ($doc->items as $i => $item): ?>
                        <?= render_template('forms/_item_row', ['item' => $item, 'settings' => $settings, 'index' => $i]) ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="items-toolbar">
            <button type="button" class="btn btn-outline-accent btn-sm" id="addRowBtn">+ Add item</button>
            <span class="text-small text-muted"><span id="itemCount"><?= count($doc->items) ?></span> item(s)</span>
        </div>
    </section>

    <!-- 4. Terms + totals -->
    <div class="row g-4">
        <div class="col-lg-7">
            <section class="docs-card h-100 mb-0">
                <div class="docs-card-head">
                    <h2><span class="step">4</span>Terms &amp; Conditions</h2>
                    <span class="text-small text-muted">One condition per line</span>
                </div>
                <div class="docs-card-body">
                    <textarea class="form-control" name="terms" id="terms" rows="8"><?= e(implode("\n", $doc->terms)) ?></textarea>
                </div>
            </section>
        </div>
        <div class="col-lg-5">
            <section class="docs-card h-100 mb-0">
                <div class="docs-card-head">
                    <h2><span class="step">5</span>Summary</h2>
                </div>
                <div class="docs-card-body">
                    <ul class="totals-list">
                        <li><span>Taxable Value</span><span id="sumSubtotal">0.00</span></li>
                        <li class="tax-split"><span>CGST</span><span id="sumCgst">0.00</span></li>
                        <li class="tax-split"><span>SGST</span><span id="sumSgst">0.00</span></li>
                        <li class="tax-igst d-none"><span>IGST</span><span id="sumIgst">0.00</span></li>
                        <li><span>Total (incl. GST)</span><span id="sumTotal">0.00</span></li>
                        <li><span>Round Off</span><span id="sumRound">0.00</span></li>
                        <li class="grand"><span><?= $isInvoice ? 'Amount Payable' : 'Quoted Amount' ?></span><span id="sumPayable"><?= e($settings['currency_symbol']) ?> 0.00</span></li>
                    </ul>
                    <div class="words-box">Amount in words is added to the PDF automatically (Indian numbering system).</div>
                </div>
            </section>
        </div>
    </div>

    <div class="action-bar mt-4">
        <span class="hint">The PDF is generated on your request and saved to your computer's Downloads folder. No data is stored on the server.</span>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-outline-accent" name="action" value="preview" formtarget="_blank" id="previewBtn">Preview PDF</button>
            <button type="submit" class="btn btn-accent px-4" name="action" value="download" id="downloadBtn">Download PDF</button>
        </div>
    </div>
</form>

<template id="itemRowTemplate">
    <?= render_template('forms/_item_row', ['item' => Document::blankItem($settings), 'settings' => $settings, 'index' => 0]) ?>
</template>
