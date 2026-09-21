<?php
/**
 * Organisation header block with the document meta box on the right.
 * Variables: $doc (Document), $org, $logo (data URI), $metaRows (array of [label, value])
 */
?>
<table class="block hdr">
    <tr>
        <td class="org">
            <table>
                <tr>
                    <?php if ($logo !== ''): ?>
                        <td class="org-logo"><img src="<?= $logo ?>" alt=""></td>
                    <?php endif; ?>
                    <td>
                        <div class="org-name"><?= e($org['name']) ?></div>
                        <?php if (!empty($org['tagline'])): ?>
                            <div class="org-tagline"><?= e($org['tagline']) ?></div>
                        <?php endif; ?>
                        <div class="org-line"><?= e($org['address']) ?></div>
                        <div class="org-line"><span class="k">Phone:</span> <?= e($org['phone']) ?> &nbsp;|&nbsp; <span class="k">Email:</span> <?= e($org['email']) ?></div>
                        <div class="org-line"><span class="k">GSTIN:</span> <strong><?= e($org['gstin']) ?></strong>
                            <?php if (!empty($org['pan'])): ?>&nbsp;|&nbsp; <span class="k">PAN:</span> <?= e($org['pan']) ?><?php endif; ?>
                        </div>
                        <div class="org-line"><span class="k">State:</span> <?= e($org['state']) ?> &nbsp;|&nbsp; <span class="k">State Code:</span> <?= e($org['state_code']) ?></div>
                    </td>
                </tr>
            </table>
        </td>
        <td class="meta">
            <table class="meta-table">
                <?php $last = count($metaRows) - 1; foreach ($metaRows as $i => [$label, $value]): ?>
                    <tr<?= $i === $last ? ' class="last"' : '' ?>>
                        <td class="k"><?= e($label) ?></td>
                        <td class="v"><?= $value === '' ? '&mdash;' : e($value) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </td>
    </tr>
</table>
