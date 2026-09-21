<?php
/**
 * Document model: builds an Invoice or Quotation from form input, validates it
 * and calculates every amount server-side (client-side totals are never trusted).
 *
 * The object only lives for the duration of one request – nothing is persisted.
 */
final class Document
{
    public const TYPE_INVOICE   = 'invoice';
    public const TYPE_QUOTATION = 'quotation';

    public const TAX_CGST_SGST = 'cgst_sgst';
    public const TAX_IGST      = 'igst';

    /** @var string invoice|quotation */
    public $type;
    /** @var array organization profile */
    public $org;
    /** @var array application settings */
    public $settings;

    public $number      = '';
    public $date        = '';   // Y-m-d
    public $po_number   = '';   // invoice only
    public $subject     = '';   // quotation only
    public $reference   = '';   // quotation only
    public $valid_until = '';   // quotation only (Y-m-d)
    public $tax_type    = self::TAX_CGST_SGST;

    /** @var array{name:string,attention:string,address:string,gstin:string,place_of_supply:string,state_code:string} */
    public $customer = [
        'name' => '', 'attention' => '', 'address' => '', 'gstin' => '', 'place_of_supply' => '', 'state_code' => '',
    ];

    /** @var array<int,array> */
    public $items = [];

    /** @var string[] terms / notes, one per line */
    public $terms = [];

    /** @var array calculated totals */
    public $totals = [];

    /** @var array<int,array> HSN/SAC wise tax summary */
    public $hsn_summary = [];

    /** @var string */
    public $amount_in_words = '';

    /** @var array<string,string> field => message */
    public $errors = [];

    private function __construct(string $type, array $org, array $settings)
    {
        $this->type     = $type;
        $this->org      = $org;
        $this->settings = $settings;
    }

    // ------------------------------------------------------------------
    // Factories
    // ------------------------------------------------------------------

    /**
     * A blank document pre-filled with sensible defaults for the entry form.
     */
    public static function defaults(string $type, array $org, array $settings): self
    {
        $doc  = new self($type, $org, $settings);
        $today = new DateTime('today');

        $doc->date   = $today->format('Y-m-d');
        $doc->number = $doc->suggestedNumberPrefix($today);

        if ($type === self::TYPE_QUOTATION) {
            $validity         = (int) ($settings['quotation_validity_days'] ?? 30);
            $doc->valid_until = (clone $today)->modify('+' . $validity . ' days')->format('Y-m-d');
            $doc->customer    = array_merge($doc->customer, $settings['quotation_customer'] ?? []);
            $doc->terms       = $org['quotation_terms'] ?? [];
        } else {
            $doc->customer = array_merge($doc->customer, $settings['invoice_customer'] ?? []);
            $doc->terms    = $org['invoice_terms'] ?? [];
        }

        $doc->items = [self::blankItem($settings)];
        $doc->calculate();
        return $doc;
    }

    /**
     * Build a document from submitted form data.
     */
    public static function fromRequest(string $type, array $org, array $settings, array $post): self
    {
        $doc = new self($type, $org, $settings);

        $doc->number      = mb_substr(post_string($post, 'number'), 0, 40);
        $doc->date        = post_string($post, 'date');
        $doc->po_number   = mb_substr(post_string($post, 'po_number'), 0, 60);
        $doc->subject     = mb_substr(post_string($post, 'subject'), 0, 200);
        $doc->reference   = mb_substr(post_string($post, 'reference'), 0, 120);
        $doc->valid_until = post_string($post, 'valid_until');
        if ($doc->isQuotation() && $doc->valid_until === '' && self::isValidDate($doc->date)) {
            $validity         = (int) ($settings['quotation_validity_days'] ?? 30);
            $doc->valid_until = (new DateTime($doc->date))->modify('+' . $validity . ' days')->format('Y-m-d');
        }
        $doc->tax_type    = post_string($post, 'tax_type') === self::TAX_IGST ? self::TAX_IGST : self::TAX_CGST_SGST;

        $customer = isset($post['customer']) && is_array($post['customer']) ? $post['customer'] : [];
        foreach (array_keys($doc->customer) as $key) {
            $doc->customer[$key] = mb_substr(post_string($customer, $key), 0, $key === 'address' ? 500 : 120);
        }
        $doc->customer['gstin'] = strtoupper(str_replace(' ', '', $doc->customer['gstin']));

        // Terms: one per line, empty lines dropped
        $termsRaw   = post_string($post, 'terms');
        $doc->terms = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $termsRaw)), 'strlen'));
        $doc->terms = array_slice($doc->terms, 0, 15);

        // Line items (parallel arrays)
        $columns = ['description', 'hsn', 'sor', 'qty', 'unit', 'rate', 'gst_rate'];
        $rows    = [];
        $count   = 0;
        foreach ($columns as $col) {
            $values = isset($post[$col]) && is_array($post[$col]) ? array_values($post[$col]) : [];
            $count  = max($count, count($values));
            $rows[$col] = $values;
        }
        $count = min($count, 100);

        for ($i = 0; $i < $count; $i++) {
            $item = [];
            foreach ($columns as $col) {
                $item[$col] = isset($rows[$col][$i]) && !is_array($rows[$col][$i]) ? trim((string) $rows[$col][$i]) : '';
            }
            // Skip rows the user left completely empty
            if ($item['description'] === '' && $item['qty'] === '' && $item['rate'] === '' && $item['hsn'] === '' && $item['sor'] === '') {
                continue;
            }
            $doc->items[] = [
                'description' => mb_substr($item['description'], 0, 500),
                'hsn'         => mb_substr(preg_replace('/\s+/', '', $item['hsn']), 0, 10),
                'sor'         => mb_substr($item['sor'], 0, 30),
                'qty'         => $item['qty'],
                'unit'        => mb_substr($item['unit'], 0, 12),
                'rate'        => $item['rate'],
                'gst_rate'    => $item['gst_rate'],
            ];
        }

        $doc->validate();
        $doc->calculate();
        return $doc;
    }

    public static function blankItem(array $settings): array
    {
        return [
            'description' => '',
            'hsn'         => '',
            'sor'         => '',
            'qty'         => '1',
            'unit'        => $settings['default_unit'] ?? 'Nos',
            'rate'        => '',
            'gst_rate'    => (string) ($settings['default_gst_rate'] ?? 18),
        ];
    }

    // ------------------------------------------------------------------
    // Validation
    // ------------------------------------------------------------------

    public function validate(): bool
    {
        $this->errors = [];
        $label        = $this->isInvoice() ? 'Invoice' : 'Quotation';

        if ($this->number === '') {
            $this->errors['number'] = $label . ' number is required.';
        }

        if (!self::isValidDate($this->date)) {
            $this->errors['date'] = 'Please enter a valid ' . strtolower($label) . ' date.';
        }

        if ($this->isQuotation()) {
            if ($this->subject === '') {
                $this->errors['subject'] = 'Subject is required for a quotation.';
            }
            if ($this->valid_until !== '' && !self::isValidDate($this->valid_until)) {
                $this->errors['valid_until'] = 'Please enter a valid date.';
            }
        }

        if ($this->customer['name'] === '') {
            $this->errors['customer.name'] = 'Customer name is required.';
        }
        if ($this->customer['gstin'] !== '' && !preg_match('/^[0-9]{2}[A-Z0-9]{13}$/', $this->customer['gstin'])) {
            $this->errors['customer.gstin'] = 'GSTIN must be 15 characters (e.g. 27AAACB2902M1ZT).';
        }

        if (count($this->items) === 0) {
            $this->errors['items'] = 'Add at least one line item.';
        }

        foreach ($this->items as $index => $item) {
            $row = $index + 1;
            if ($item['description'] === '') {
                $this->errors['items.' . $index . '.description'] = 'Row ' . $row . ': description is required.';
            }
            if (!is_numeric($item['qty']) || (float) $item['qty'] <= 0) {
                $this->errors['items.' . $index . '.qty'] = 'Row ' . $row . ': quantity must be greater than zero.';
            }
            if (!is_numeric($item['rate']) || (float) $item['rate'] < 0) {
                $this->errors['items.' . $index . '.rate'] = 'Row ' . $row . ': enter a valid rate.';
            }
            if (!is_numeric($item['gst_rate']) || (float) $item['gst_rate'] < 0 || (float) $item['gst_rate'] > 100) {
                $this->errors['items.' . $index . '.gst_rate'] = 'Row ' . $row . ': GST % must be between 0 and 100.';
            }
            if ($item['hsn'] !== '' && !preg_match('/^[0-9]{2,8}$/', $item['hsn'])) {
                $this->errors['items.' . $index . '.hsn'] = 'Row ' . $row . ': HSN/SAC must be 2 to 8 digits.';
            }
        }

        return empty($this->errors);
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    // ------------------------------------------------------------------
    // Calculation
    // ------------------------------------------------------------------

    /**
     * Compute every line, total and the HSN-wise tax summary.
     * Invalid numeric input is treated as zero so the form can still be re-displayed.
     */
    public function calculate(): void
    {
        $subtotal = 0.0;
        $tax      = 0.0;
        $groups   = [];

        foreach ($this->items as $i => $item) {
            $qty  = is_numeric($item['qty']) ? (float) $item['qty'] : 0.0;
            $rate = is_numeric($item['rate']) ? (float) $item['rate'] : 0.0;
            $gst  = is_numeric($item['gst_rate']) ? (float) $item['gst_rate'] : 0.0;

            $taxable   = round($qty * $rate, 2);
            $gstAmount = round($taxable * $gst / 100, 2);
            $lineTotal = round($taxable + $gstAmount, 2);

            $this->items[$i]['qty_num']      = $qty;
            $this->items[$i]['rate_num']     = $rate;
            $this->items[$i]['gst_rate_num'] = $gst;
            $this->items[$i]['taxable']      = $taxable;
            $this->items[$i]['gst_amount']   = $gstAmount;
            $this->items[$i]['line_total']   = $lineTotal;

            $subtotal += $taxable;
            $tax      += $gstAmount;

            $key = ($item['hsn'] !== '' ? $item['hsn'] : '-') . '|' . rtrim(rtrim(number_format($gst, 2, '.', ''), '0'), '.');
            if (!isset($groups[$key])) {
                $groups[$key] = ['hsn' => $item['hsn'] !== '' ? $item['hsn'] : '-', 'rate' => $gst, 'taxable' => 0.0, 'tax' => 0.0];
            }
            $groups[$key]['taxable'] += $taxable;
            $groups[$key]['tax']     += $gstAmount;
        }

        $subtotal   = round($subtotal, 2);
        $tax        = round($tax, 2);
        $grandTotal = round($subtotal + $tax, 2);
        $rounded    = round($grandTotal);          // nearest rupee
        $roundOff   = round($rounded - $grandTotal, 2);

        $split = $this->tax_type === self::TAX_IGST ? 1 : 2;

        $this->totals = [
            'subtotal'    => $subtotal,
            'tax'         => $tax,
            'cgst'        => $split === 2 ? round($tax / 2, 2) : 0.0,
            'sgst'        => $split === 2 ? round($tax - round($tax / 2, 2), 2) : 0.0,
            'igst'        => $split === 1 ? $tax : 0.0,
            'grand_total' => $grandTotal,
            'round_off'   => $roundOff,
            'payable'     => $rounded,
        ];

        $this->hsn_summary = [];
        foreach ($groups as $g) {
            $g['taxable'] = round($g['taxable'], 2);
            $g['tax']     = round($g['tax'], 2);
            if ($split === 2) {
                $g['cgst_rate']   = $g['rate'] / 2;
                $g['sgst_rate']   = $g['rate'] / 2;
                $g['cgst_amount'] = round($g['tax'] / 2, 2);
                $g['sgst_amount'] = round($g['tax'] - $g['cgst_amount'], 2);
                $g['igst_rate']   = 0.0;
                $g['igst_amount'] = 0.0;
            } else {
                $g['cgst_rate'] = $g['sgst_rate'] = 0.0;
                $g['cgst_amount'] = $g['sgst_amount'] = 0.0;
                $g['igst_rate']   = $g['rate'];
                $g['igst_amount'] = $g['tax'];
            }
            $this->hsn_summary[] = $g;
        }

        $this->amount_in_words = amount_in_words(
            $rounded,
            $this->settings['currency_name'] ?? 'Rupees'
        );
    }

    // ------------------------------------------------------------------
    // Presentation helpers
    // ------------------------------------------------------------------

    public function isInvoice(): bool
    {
        return $this->type === self::TYPE_INVOICE;
    }

    public function isQuotation(): bool
    {
        return $this->type === self::TYPE_QUOTATION;
    }

    public function typeLabel(): string
    {
        return $this->isInvoice() ? 'Invoice' : 'Quotation';
    }

    public function title(): string
    {
        return $this->isInvoice()
            ? ($this->settings['invoice_title'] ?? 'TAX INVOICE')
            : ($this->settings['quotation_title'] ?? 'QUOTATION');
    }

    /**
     * File name of the generated PDF, e.g. "Friends-Enterprises-Invoice-FE-INV-26-27-001.pdf".
     */
    public function fileName(): string
    {
        return safe_filename($this->org['name'] . '-' . $this->typeLabel() . '-' . $this->number) . '.pdf';
    }

    /**
     * Suggested numbering prefix including the Indian financial year (Apr–Mar), e.g. "FE/INV/26-27/".
     */
    public function suggestedNumberPrefix(DateTime $date): string
    {
        $year  = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $start = $month >= 4 ? $year : $year - 1;
        $fy    = substr((string) $start, -2) . '-' . substr((string) ($start + 1), -2);
        $key   = $this->isInvoice() ? 'invoice_prefix' : 'quotation_prefix';
        return ($this->org['numbering'][$key] ?? '') . $fy . '/';
    }

    /**
     * Number of blank rows to pad into the PDF items table.
     */
    public function paddingRows(): int
    {
        $min = (int) ($this->settings['min_item_rows'] ?? 0);
        if ($this->isQuotation()) {
            $min -= 3; // quotation carries the subject/intro block, keep a single page for typical documents
        }
        return max(0, $min - count($this->items));
    }

    /**
     * Human readable GST rate ("18" or "2.5").
     */
    public static function rateLabel($rate): string
    {
        $rate = (float) $rate;
        return rtrim(rtrim(number_format($rate, 2, '.', ''), '0'), '.');
    }

    /**
     * Quantity formatted without needless decimals.
     */
    public static function qtyLabel($qty): string
    {
        $qty = (float) $qty;
        return rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.');
    }

    private static function isValidDate(string $value): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return false;
        }
        $dt = DateTime::createFromFormat('Y-m-d', $value);
        if (!$dt || $dt->format('Y-m-d') !== $value) {
            return false;
        }
        $year = (int) $dt->format('Y');
        return $year >= 2000 && $year <= 2100;
    }
}
