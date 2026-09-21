<?php
/**
 * Shared stylesheet for the PDF templates (dompdf – CSS 2.1 subset).
 * Variables: $theme (primary, light, band)
 */
$primary = $theme['primary'];
$light   = $theme['light'];
$band    = $theme['band'];
?>
@page { margin: 26pt 30pt 40pt 30pt; }
* { box-sizing: border-box; }
body { font-family: "DejaVu Sans", sans-serif; font-size: 8.2pt; color: #1f2933; line-height: 1.0; margin: 0; padding: 0; }
table { border-collapse: collapse; width: 100%; }
td, th { vertical-align: top; }
p { margin: 0; }
strong, b { font-weight: bold; }

.title-row td { padding: 0 0 5pt 0; vertical-align: bottom; }
.doc-title { font-size: 18pt; font-weight: bold; color: <?= $primary ?>; letter-spacing: 3pt; text-align: center; }
.doc-copy { font-size: 6.8pt; line-height: 1.0; color: #6b7280; text-align: right; white-space: nowrap; }

/* Blocks are sibling tables (not nested in one frame) so dompdf can break pages between them.
   Each block draws its own outer border; the top border is omitted where it joins the block above. */
.block { border: 1pt solid #3b3f46; }
.block.joined { border-top: none; }

/* Organisation header */
.hdr td { padding: 0; }
.hdr .org { background: <?= $light ?>; width: 62%; border-right: 1pt solid #3b3f46; padding: 6pt 9pt; }
.hdr .meta { background: <?= $band ?>; width: 38%; }
.org-logo { width: 58pt; padding-right: 8pt; }
.org-logo img { width: 54pt; }
.org-name { font-size: 14pt; font-weight: bold; color: <?= $primary ?>; line-height: 0.95; margin-bottom: 1pt; }
.org-tagline { font-size: 7.5pt; color: #4b5563; margin-bottom: 3pt; line-height: 1.0; }
.org-line { font-size: 7.7pt; color: #1f2933; line-height: 1.05; }
.org-line .k { color: #4b5563; }
.meta-table td { padding: 3pt 8pt; border-bottom: 1pt solid #3b3f46; font-size: 7.8pt; line-height: 1.05; white-space: nowrap; }
.meta-table td.k { color: #1f2933; width: 46%; }
.meta-table td.v { font-weight: bold; text-align: right; }
.meta-table tr.last td { border-bottom: none; }

/* Party details */
.party-head td { background: <?= $band ?>; border-bottom: 1pt solid #3b3f46; padding: 3pt 9pt; font-weight: bold; font-size: 8pt; }
.party-body td { padding: 5pt 9pt; font-size: 8pt; line-height: 1.05; background: <?= $light ?>; }
.party-body td.left, .party-head td.left { width: 50%; border-right: 1pt solid #3b3f46; }
.party-name { font-weight: bold; font-size: 9pt; color: #111827; }
.party-body .k { color: #4b5563; }

/* Line items */
.items th { background: <?= $band ?>; border: 1pt solid #3b3f46; padding: 3.5pt 3pt; font-size: 7.1pt; line-height: 1.0; text-align: center; font-weight: bold; color: #111827; }
.items td { border: 1pt solid #3b3f46; padding: 2.6pt 3.5pt; font-size: 7.6pt; line-height: 1.02; }
.items td.num { text-align: right; white-space: nowrap; }
.items td.ctr { text-align: center; }
.items tr.pad td { height: 12pt; border-top: none; border-bottom: none; }
.items tr.pad-last td { border-bottom: 1pt solid #3b3f46; }
.items .desc { width: 33%; }

/* Totals + bank */
.summary td { padding: 0; }
.summary .bank { width: 55%; border-right: 1pt solid #3b3f46; padding: 5pt 9pt; background: <?= $light ?>; }
.summary .totals { width: 45%; }
.bank-title { font-weight: bold; font-size: 8.4pt; color: <?= $primary ?>; margin-bottom: 2pt; }
.bank-table td { padding: 0.5pt 0; font-size: 7.7pt; line-height: 1.05; }
.bank-table td.k { color: #4b5563; width: 38%; }
.words { margin-top: 5pt; padding-top: 4pt; border-top: 1pt dashed #9aa0a6; font-size: 7.7pt; line-height: 1.05; }
.words .k { color: #4b5563; }
.totals-table td { padding: 2.8pt 8pt; border-bottom: 1pt solid #3b3f46; font-size: 7.9pt; line-height: 1.05; }
.totals-table td.k { color: #1f2933; }
.totals-table td.v { text-align: right; white-space: nowrap; font-weight: bold; }
.totals-table tr.grand td { background: <?= $band ?>; font-size: 9.4pt; font-weight: bold; border-bottom: none; padding: 4.5pt 8pt; color: #111827; }

/* HSN summary */
.hsn th { background: <?= $band ?>; border: 1pt solid #3b3f46; padding: 2.6pt; font-size: 7.1pt; line-height: 1.0; text-align: center; }
.hsn td { border: 1pt solid #3b3f46; padding: 2.2pt 5pt; font-size: 7.6pt; line-height: 1.02; }
.hsn td.num { text-align: right; white-space: nowrap; }
.hsn td.ctr { text-align: center; }
.hsn tr.total td { font-weight: bold; background: <?= $light ?>; }

/* Terms + signature */
.foot td { padding: 0; }
.foot .terms { width: 62%; border-right: 1pt solid #3b3f46; padding: 5pt 9pt; background: <?= $light ?>; }
.foot .sign { width: 38%; padding: 5pt 9pt; text-align: center; background: <?= $light ?>; }
.sec-title { font-weight: bold; font-size: 8.2pt; color: <?= $primary ?>; margin-bottom: 1.5pt; }
.terms ol { margin: 0 0 0 11pt; padding: 0; }
.terms li { font-size: 7.3pt; line-height: 1.0; margin-bottom: 1pt; }
.sign .for { font-weight: bold; font-size: 8.2pt; }
.sign img { width: 56pt; margin: 3pt 0; }
.sign .who { font-size: 7.6pt; line-height: 1.0; color: #374151; border-top: 1pt solid #9aa0a6; display: inline-block; padding: 2pt 14pt 0 14pt; margin-top: 2pt; }

.band td { background: <?= $band ?>; text-align: center; font-weight: bold; font-size: 9.5pt; color: <?= $primary ?>; padding: 4pt; letter-spacing: 1pt; }

/* Quotation letter parts */
.kv td { padding: 1pt 0; font-size: 8pt; line-height: 1.05; vertical-align: top; }
.kv td.k { width: 36%; color: #4b5563; white-space: nowrap; padding-right: 6pt; }
.subject { padding: 4pt 9pt; line-height: 1.05; border: 1pt solid #3b3f46; border-top: none; background: <?= $light ?>; font-size: 8.6pt; }
.intro { padding: 5pt 9pt 6pt 9pt; font-size: 8.2pt; line-height: 1.05; border-left: 1pt solid #3b3f46; border-right: 1pt solid #3b3f46; }

.avoid-break { page-break-inside: avoid; }
.spacer { height: 4pt; }
.muted { color: #6b7280; }
.nowrap { white-space: nowrap; }
