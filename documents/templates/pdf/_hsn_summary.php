<?php
/**
 * HSN/SAC wise tax summary.
 * Variables: $doc (Document)
 */
$igst = $doc->tax_type === Document::TAX_IGST;
$t    = $doc->totals;
?>
<table class="hsn">
    <thead>
        <tr>
            <th rowspan="2" style="width:5%">Sr.</th>
            <th rowspan="2" style="width:16%">HSN/SAC</th>
            <th rowspan="2" style="width:20%">Taxable Value</th>
            <?php if ($igst): ?>
                <th colspan="2">Integrated Tax (IGST)</th>
            <?php else: ?>
                <th colspan="2">Central Tax (CGST)</th>
                <th colspan="2">State Tax (SGST)</th>
            <?php endif; ?>
            <th rowspan="2" style="width:16%">Total Tax</th>
        </tr>
        <tr>
            <?php if ($igst): ?>
                <th style="width:10%">Rate</th><th>Amount</th>
            <?php else: ?>
                <th style="width:8%">Rate</th><th>Amount</th>
                <th style="width:8%">Rate</th><th>Amount</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($doc->hsn_summary as $i => $g): ?>
            <tr>
                <td class="ctr"><?= $i + 1 ?></td>
                <td class="ctr"><?= e($g['hsn']) ?></td>
                <td class="num"><?= e(format_inr($g['taxable'])) ?></td>
                <?php if ($igst): ?>
                    <td class="ctr"><?= e(Document::rateLabel($g['igst_rate'])) ?>%</td>
                    <td class="num"><?= e(format_inr($g['igst_amount'])) ?></td>
                <?php else: ?>
                    <td class="ctr"><?= e(Document::rateLabel($g['cgst_rate'])) ?>%</td>
                    <td class="num"><?= e(format_inr($g['cgst_amount'])) ?></td>
                    <td class="ctr"><?= e(Document::rateLabel($g['sgst_rate'])) ?>%</td>
                    <td class="num"><?= e(format_inr($g['sgst_amount'])) ?></td>
                <?php endif; ?>
                <td class="num"><?= e(format_inr($g['tax'])) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr class="total">
            <td></td>
            <td class="ctr">Total</td>
            <td class="num"><?= e(format_inr($t['subtotal'])) ?></td>
            <?php if ($igst): ?>
                <td></td>
                <td class="num"><?= e(format_inr($t['igst'])) ?></td>
            <?php else: ?>
                <td></td>
                <td class="num"><?= e(format_inr($t['cgst'])) ?></td>
                <td></td>
                <td class="num"><?= e(format_inr($t['sgst'])) ?></td>
            <?php endif; ?>
            <td class="num"><?= e(format_inr($t['tax'])) ?></td>
        </tr>
    </tbody>
</table>
