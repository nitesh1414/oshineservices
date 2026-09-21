<?php
/**
 * Tax Invoice PDF template.
 * Variables: $doc (Document), $org, $settings, $theme, $logo, $stamp
 */
$c        = $doc->customer;
$metaRows = [
    ['Invoice No.', $doc->number],
    ['Invoice Date', format_date($doc->date)],
    ['PO No.', $doc->po_number],
    ['BPCL Vendor Code', (string) $org['vendor_code']],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= e($doc->title() . ' ' . $doc->number) ?></title>
    <style><?= render_template('pdf/styles', ['theme' => $theme]) ?></style>
</head>
<body>

<table class="title-row">
    <tr>
        <td style="width:20%"></td>
        <td class="doc-title"><?= e($doc->title()) ?></td>
        <td class="doc-copy" style="width:20%">Original for Recipient</td>
    </tr>
</table>

<?= render_template('pdf/_org_header', ['doc' => $doc, 'org' => $org, 'logo' => $logo, 'metaRows' => $metaRows]) ?>

<table class="block joined party">
                <tr class="party-head">
                    <td class="left">Bill To</td>
                    <td>Billing Address</td>
                </tr>
                <tr class="party-body">
                    <td class="left">
                        <?php if ($c['attention'] !== ''): ?><div><?= e($c['attention']) ?>,</div><?php endif; ?>
                        <div class="party-name"><?= e($c['name']) ?></div>
                        <?php if ($c['gstin'] !== ''): ?><div><span class="k">GSTIN:</span> <strong><?= e($c['gstin']) ?></strong></div><?php endif; ?>
                        <?php if ($c['place_of_supply'] !== ''): ?><div><span class="k">Place of Supply:</span> <?= e($c['place_of_supply']) ?><?= $c['state_code'] !== '' ? ' &nbsp;<span class="k">State Code:</span> ' . e($c['state_code']) : '' ?></div><?php endif; ?>
                    </td>
                    <td>
                        <?= $c['address'] !== '' ? nl2br_e($c['address']) : '&mdash;' ?>
                    </td>
                </tr>
            </table>

            <?= render_template('pdf/_items', ['doc' => $doc]) ?>

            <table class="block joined summary avoid-break">
                <tr>
                    <td class="bank">
                        <div class="bank-title">Bank Details</div>
                        <table class="bank-table">
                            <tr><td class="k">Account Name</td><td><?= e($org['bank']['account_name']) ?></td></tr>
                            <tr><td class="k">Bank Name</td><td><?= e($org['bank']['bank_name']) ?></td></tr>
                            <tr><td class="k">Branch</td><td><?= e($org['bank']['branch']) ?></td></tr>
                            <tr><td class="k">Account No.</td><td><strong><?= e($org['bank']['account_no']) ?></strong></td></tr>
                            <tr><td class="k">IFSC Code</td><td><strong><?= e($org['bank']['ifsc']) ?></strong></td></tr>
                        </table>
                        <div class="words"><span class="k">Amount in words:</span> <strong><?= e($doc->amount_in_words) ?></strong></div>
                    </td>
                    <td class="totals">
                        <?= render_template('pdf/_totals', ['doc' => $doc]) ?>
                    </td>
                </tr>
            </table>

<div class="spacer"></div>

<div class="avoid-break">
    <?= render_template('pdf/_hsn_summary', ['doc' => $doc]) ?>
</div>

<div class="spacer"></div>

<div class="avoid-break">
            <table class="block foot">
                <tr>
                    <td class="terms">
                        <div class="sec-title">Terms &amp; Conditions</div>
                        <?php if (count($doc->terms) > 0): ?>
                            <ol>
                                <?php foreach ($doc->terms as $term): ?>
                                    <li><?= e($term) ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php else: ?>
                            <div class="muted">&mdash;</div>
                        <?php endif; ?>
                    </td>
                    <td class="sign">
                        <div class="for">For <?= e($org['name']) ?></div>
                        <?php if ($stamp !== ''): ?><div><img src="<?= $stamp ?>" alt=""></div><?php else: ?><div style="height:60pt"></div><?php endif; ?>
                        <div class="who"><?= e($org['signatory']) ?></div>
                    </td>
                </tr>
            </table>
            <table class="block joined band">
                <tr><td>THANK YOU FOR YOUR BUSINESS!</td></tr>
            </table>
</div>

</body>
</html>
