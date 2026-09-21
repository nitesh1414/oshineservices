<?php
/**
 * One editable line-item row. Variables: $item, $settings, $index
 */
$units    = $settings['units'] ?? ['Nos'];
$gstRates = $settings['gst_rates'] ?? [0, 5, 12, 18, 28];
$unit     = $item['unit'] !== '' ? $item['unit'] : ($settings['default_unit'] ?? 'Nos');
if (!in_array($unit, $units, true)) {
    $units[] = $unit;
}
$gst = $item['gst_rate'] !== '' ? (string) (float) $item['gst_rate'] : (string) ($settings['default_gst_rate'] ?? 18);
$gstKnown = false;
foreach ($gstRates as $r) {
    if ((string) (float) $r === $gst) {
        $gstKnown = true;
    }
}
?>
<tr class="item-row">
    <td class="sr"><span class="row-no"><?= $index + 1 ?></span></td>
    <td><textarea class="form-control form-control-sm f-desc" name="description[]" rows="1" maxlength="500" placeholder="Description of goods / services" required><?= e($item['description']) ?></textarea></td>
    <td><input type="text" class="form-control form-control-sm f-hsn" name="hsn[]" value="<?= e($item['hsn']) ?>" maxlength="8" inputmode="numeric" pattern="[0-9]{2,8}" placeholder="9954"></td>
    <td><input type="text" class="form-control form-control-sm f-sor" name="sor[]" value="<?= e($item['sor']) ?>" maxlength="30"></td>
    <td><input type="number" class="form-control form-control-sm num f-qty" name="qty[]" value="<?= e($item['qty']) ?>" min="0.001" step="any" required></td>
    <td>
        <select class="form-select form-select-sm f-unit" name="unit[]">
            <?php foreach ($units as $u): ?>
                <option value="<?= e($u) ?>"<?= $u === $unit ? ' selected' : '' ?>><?= e($u) ?></option>
            <?php endforeach; ?>
        </select>
    </td>
    <td><input type="number" class="form-control form-control-sm num f-rate" name="rate[]" value="<?= e($item['rate']) ?>" min="0" step="0.01" required placeholder="0.00"></td>
    <td>
        <select class="form-select form-select-sm f-gst" name="gst_rate[]">
            <?php foreach ($gstRates as $r): ?>
                <option value="<?= e((string) $r) ?>"<?= (string) (float) $r === $gst ? ' selected' : '' ?>><?= e((string) $r) ?>%</option>
            <?php endforeach; ?>
            <?php if (!$gstKnown): ?><option value="<?= e($gst) ?>" selected><?= e($gst) ?>%</option><?php endif; ?>
        </select>
    </td>
    <td class="calc c-taxable">0.00</td>
    <td class="calc c-gst">0.00</td>
    <td class="calc c-total">0.00</td>
    <td class="text-center"><button type="button" class="btn-remove" title="Remove item" aria-label="Remove item">&#10005;</button></td>
</tr>
