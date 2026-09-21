<?php
/**
 * Organization profiles used by the document generator.
 *
 * Each organization can issue both Invoices and Quotations. The numeric key is
 * the value used in the URL (create_invoice.php?id=1) and therefore must not
 * change once documents have been issued.
 *
 * All paths are relative to the /documents directory.
 */
return [

    1 => [
        'slug'        => 'friends-enterprises',
        'name'        => 'Friends Enterprises',
        'tagline'     => 'Civil, Electrical & Maintenance Contractors',
        'address'     => 'Plot No. 97, Behind Buddha Vihar, Nav Ekar, Chandramani Nagar, Nagpur - 440027',
        'city'        => 'Nagpur',
        'phone'       => '+91-9665861776',
        'email'       => 'friendsent4821@gmail.com',
        'gstin'       => '27EHXPS8451K1Z4',
        'pan'         => 'EHXPS8451K',
        'state'       => 'Maharashtra',
        'state_code'  => '27',
        'vendor_code' => '381776',          // BPCL vendor code
        'logo'        => 'images/fe-logo-small.png',
        'stamp'       => 'images/fe-stamp.png',
        'signatory'   => 'Authorised Signatory',
        'bank' => [
            'account_name' => 'Friends Enterprises',
            'bank_name'    => 'Canara Bank',
            'branch'       => 'Khamla, Nagpur',
            'account_no'   => '120031969315',
            'ifsc'         => 'CNRB0002810',
        ],
        // Colour theme (kept from the original design)
        'theme' => [
            'primary' => '#0f4e84',   // headings, title
            'light'   => '#f6fafd',   // light panels
            'band'    => '#b3d6ec',   // table headers / bands
            'accent'  => '#40289f',   // form UI accent
        ],
        'numbering' => [
            'invoice_prefix'   => 'FE/INV/',
            'quotation_prefix' => 'FE/QTN/',
        ],
        'invoice_terms' => [
            'Make all cheques payable to "Friends Enterprises".',
            'No responsibility after delivery.',
            'Payment must be made within 15 days from the date of invoice.',
            'Subject to Nagpur jurisdiction.',
            'We declare that this invoice shows the actual price of the goods/services described and that all particulars are true and correct.',
        ],
        'quotation_terms' => [
            'Prices quoted are exclusive of GST unless stated otherwise; GST will be charged as applicable.',
            'This quotation is valid for 30 days from the date of issue.',
            'Payment: within 15 days of submission of invoice.',
            'Any additional work not covered in the above scope will be charged extra as per actuals.',
            'Subject to Nagpur jurisdiction.',
        ],
    ],

    2 => [
        'slug'        => 'oshin-services',
        'name'        => 'Oshin Services',
        'tagline'     => 'Facility Management & Maintenance Services',
        'address'     => 'Plot No. 97, Behind Buddha Vihar, Nav Ekar, Chandramani Nagar, Nagpur - 440027',
        'city'        => 'Nagpur',
        'phone'       => '+91-7385283073',
        'email'       => 'admin@oshinservices.in',
        'gstin'       => '27EHXPS8451K1Z4',
        'pan'         => 'EHXPS8451K',
        'state'       => 'Maharashtra',
        'state_code'  => '27',
        'vendor_code' => '',
        'logo'        => 'images/os-logo-small.png',
        'stamp'       => 'images/os-stamp.png',
        'signatory'   => 'Authorised Signatory',
        'bank' => [
            'account_name' => 'Oshin Services',
            'bank_name'    => 'Canara Bank',
            'branch'       => 'Khamla, Nagpur',
            'account_no'   => '120031969315',
            'ifsc'         => 'CNRB0002810',
        ],
        'theme' => [
            'primary' => '#d97b00',
            'light'   => '#fdf3e3',
            'band'    => '#fbd190',
            'accent'  => '#e19219',
        ],
        'numbering' => [
            'invoice_prefix'   => 'OS/INV/',
            'quotation_prefix' => 'OS/QTN/',
        ],
        'invoice_terms' => [
            'Make all cheques payable to "Oshin Services".',
            'No responsibility after delivery.',
            'Payment must be made within 15 days from the date of invoice.',
            'Subject to Nagpur jurisdiction.',
            'We declare that this invoice shows the actual price of the goods/services described and that all particulars are true and correct.',
        ],
        'quotation_terms' => [
            'Prices quoted are exclusive of GST unless stated otherwise; GST will be charged as applicable.',
            'This quotation is valid for 30 days from the date of issue.',
            'Payment: within 15 days of submission of invoice.',
            'Any additional work not covered in the above scope will be charged extra as per actuals.',
            'Subject to Nagpur jurisdiction.',
        ],
    ],
];
