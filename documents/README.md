# Invoice & Quotation Generator

PHP application that produces GST tax invoices and quotations as PDF files for
two organizations:

| id | Organization        | Theme  |
|----|---------------------|--------|
| 1  | Friends Enterprises | blue   |
| 2  | Oshin Services      | orange |

The generated document is rendered on the server and **downloaded straight to
the user's computer**. Nothing is written to a database or to disk – the
application keeps no record of the documents that were created.

## Requirements

* PHP 7.4 or newer (8.x recommended) with the `mbstring`, `dom` and `gd`
  extensions (standard on shared hosting).
* Apache (`.htaccess` files protect the internal folders) or any web server
  that can run PHP.
* No database.

The PDF engine (dompdf) ships in `vendor/`; no `composer install` is needed.

## Deploying

Upload the whole repository (website + `documents/` folder) to the web root.
Make sure `documents/storage/` is writable by PHP – it is only used for
dompdf's font cache and temporary files, never for document data. If it is not
writable the application still works, it simply renders a little slower.

## Signing in

The **Admin** button on the website opens the sign-in popup. The password is
verified on the server (`documents/login.php`), not in the browser.

Change the password in `documents/config/settings.php`:

```php
'admin_password' => '1234',
```

You may also store a hash instead of the plain password:

```php
'admin_password' => password_hash('my-secret', PASSWORD_DEFAULT),
```

(generate the hash once and paste the resulting `$2y$...` string).

## Editing organization details

All names, addresses, GSTIN/PAN, bank accounts, colours, numbering prefixes and
default terms live in `documents/config/organizations.php`. Global options such
as GST rates, units, quotation validity and document titles are in
`documents/config/settings.php`.

## Folder layout

```
documents/
├── index.php              dashboard / sign-in
├── login.php              sign-in handler for the website popup
├── logout.php
├── create_invoice.php     invoice form + PDF   (?id=1|2)
├── create_quote.php       quotation form + PDF (?id=1|2)
├── assets/                UI stylesheet and JavaScript
├── config/                organizations.php, settings.php
├── includes/              bootstrap, helpers, Document model, PDF streaming, controller
├── templates/
│   ├── layout/            page shell
│   ├── pages/             dashboard, login
│   ├── forms/             document entry form
│   └── pdf/               invoice / quotation PDF templates
├── images/                logos and stamps used in the PDFs
├── storage/               runtime cache (ignored by git)
└── vendor/                dompdf and its dependencies
```

## How a document is produced

1. `create_invoice.php?id=N` / `create_quote.php?id=N` show the entry form
   (pre-filled with the organization defaults and the default customer).
2. Line totals, GST split (CGST/SGST or IGST), round-off and the payable amount
   are computed live in the browser and **re-computed on the server** when the
   form is submitted.
3. The server validates the input, renders the HTML template with dompdf and
   returns the PDF with `Content-Disposition: attachment`, so the browser saves
   it as `<Organization>-<Invoice|Quotation>-<number>.pdf`. *Preview PDF*
   opens the same document inline in a new tab instead.
