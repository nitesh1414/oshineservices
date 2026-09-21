<?php
/**
 * Line items table.
 * Variables: $doc (Document)
 */
$pad = $doc->paddingRows();
?>
<table class="items joined">
    <thead>
        <tr>
            <th style="width:4%">Sr.<br>No.</th>
            <th class="desc">Item Description</th>
            <th style="width:8%">HSN/SAC</th>
            <th style="width:6%">SOR</th>
            <th style="width:6.5%">Qty</th>
            <th style="width:6%">Unit</th>
            <th style="width:9.5%">Rate</th>
            <th style="width:10%">Taxable Value</th>
            <th style="width:5.5%">GST %</th>
            <th style="width:9.5%">GST Amount</th>
            <th style="width:11%">Line Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($doc->items as $i => $item): ?>
            <tr>
                <td class="ctr"><?= $i + 1 ?></td>
                <td><?= nl2br_e($item['description']) ?></td>
                <td class="ctr"><?= e($item['hsn']) ?></td>
                <td class="ctr"><?= e($item['sor']) ?></td>
                <td class="num"><?= e(Document::qtyLabel($item['qty_num'])) ?></td>
                <td class="ctr"><?= e($item['unit']) ?></td>
                <td class="num"><?= e(format_inr($item['rate_num'])) ?></td>
                <td class="num"><?= e(format_inr($item['taxable'])) ?></td>
                <td class="ctr"><?= e(Document::rateLabel($item['gst_rate_num'])) ?>%</td>
                <td class="num"><?= e(format_inr($item['gst_amount'])) ?></td>
                <td class="num"><?= e(format_inr($item['line_total'])) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php for ($r = 0; $r < $pad; $r++): ?>
            <tr class="pad<?= $r === $pad - 1 ? ' pad-last' : '' ?>">
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>
