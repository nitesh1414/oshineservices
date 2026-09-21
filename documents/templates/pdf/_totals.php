<?php
/**
 * Totals box (right column of the summary band).
 * Variables: $doc (Document)
 */
$t    = $doc->totals;
$igst = $doc->tax_type === Document::TAX_IGST;

// Rate labels for the tax rows (shown when every line shares one rate)
$rates = [];
foreach ($doc->items as $item) {
    $rates[Document::rateLabel($item['gst_rate_num'])] = true;
}
$rateLabel = count($rates) === 1 ? (float) array_key_first($rates) : null;
$half      = $rateLabel !== null ? Document::rateLabel($rateLabel / 2) . '%' : '';
$full      = $rateLabel !== null ? Document::rateLabel($rateLabel) . '%' : '';
?>
<table class="totals-table">
    <tr>
        <td class="k">Taxable Value</td>
        <td class="v"><?= e(format_inr($t['subtotal'])) ?></td>
    </tr>
    <?php if ($igst): ?>
        <tr>
            <td class="k">IGST <?= $full !== '' ? '@ ' . e($full) : '' ?></td>
            <td class="v"><?= e(format_inr($t['igst'])) ?></td>
        </tr>
    <?php else: ?>
        <tr>
            <td class="k">CGST <?= $half !== '' ? '@ ' . e($half) : '' ?></td>
            <td class="v"><?= e(format_inr($t['cgst'])) ?></td>
        </tr>
        <tr>
            <td class="k">SGST <?= $half !== '' ? '@ ' . e($half) : '' ?></td>
            <td class="v"><?= e(format_inr($t['sgst'])) ?></td>
        </tr>
    <?php endif; ?>
    <tr>
        <td class="k">Total (incl. GST)</td>
        <td class="v"><?= e(format_inr($t['grand_total'])) ?></td>
    </tr>
    <tr>
        <td class="k">Round Off</td>
        <td class="v"><?= e(($t['round_off'] > 0 ? '+' : ($t['round_off'] < 0 ? '-' : '')) . format_inr(abs($t['round_off']))) ?></td>
    </tr>
    <tr class="grand">
        <td class="k"><?= $doc->isInvoice() ? 'Amount Payable' : 'Quoted Amount' ?></td>
        <td class="v"><?= e(money($t['payable'])) ?></td>
    </tr>
</table>
