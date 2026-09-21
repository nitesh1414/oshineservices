<?php
/**
 * Application settings for the document generator.
 *
 * Nothing is persisted by the application: documents are generated on the fly
 * and streamed to the browser as PDF files. The only server-side state is the
 * PHP session flag that remembers a successful admin sign-in.
 */
return [

    'app_name'   => 'Oshin Services · Document Generator',
    'site_url'   => '../index.html',      // link back to the main website

    /*
     * Admin password used by the "Admin" sign-in on the website.
     * Either a plain-text value or a password_hash() string, e.g.
     *   php -r "echo password_hash('my-secret', PASSWORD_DEFAULT);"
     */
    'admin_password' => '1234',

    'session_name' => 'osdocs',

    // Document defaults
    'currency_symbol'  => '₹',
    'currency_name'    => 'Rupees',
    'default_gst_rate' => 18,
    'default_unit'     => 'Nos',
    'gst_rates'        => [0, 5, 12, 18, 28],
    'units'            => ['Nos', 'Sq.Ft', 'Sq.Mtr', 'R.Ft', 'R.Mtr', 'Kg', 'Ltr', 'Set', 'Lot', 'Job', 'Hrs', 'Days', 'Month'],
    'quotation_validity_days' => 30,

    // PDF layout
    'paper'            => 'A4',
    'invoice_title'    => 'TAX INVOICE',
    'quotation_title'  => 'QUOTATION',
    'min_item_rows'    => 7,    // blank rows padded into the items table so short documents keep the layout

    /*
     * Default customer shown in the Invoice form (editable per document).
     */
    'invoice_customer' => [
        'name'            => 'Bharat Petroleum Corporation Limited',
        'attention'       => 'The Territory Manager (RS)',
        'address'         => "Business Process Excellence Centre (BPEC), 8th Floor, BPCL Office Complex,\nPlot No. 6, Sector-2, Behind Cidco Garden, Kharghar, Navi Mumbai - 410210",
        'gstin'           => '27AAACB2902M1ZT',
        'place_of_supply' => 'Nagpur, Maharashtra',
        'state_code'      => '27',
    ],

    /*
     * Default addressee shown in the Quotation form (editable per document).
     */
    'quotation_customer' => [
        'name'            => 'M/s Bharat Petroleum Corporation Ltd.',
        'attention'       => '',
        'address'         => "Nagpur Divisional Office, Civil Lines,\nNagpur - 440001",
        'gstin'           => '27AAACB2902M1ZT',
        'place_of_supply' => 'Nagpur, Maharashtra',
        'state_code'      => '27',
    ],
];
