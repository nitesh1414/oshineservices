<?php
/**
 * Shared controller for create_invoice.php and create_quote.php.
 *
 *   GET  → show the entry form (pre-filled with defaults)
 *   POST → validate, calculate and stream the PDF; on validation errors the
 *          form is shown again with the entered values and messages.
 *
 * Expects $documentType to be defined by the including script.
 */

require_once __DIR__ . '/bootstrap.php';

if (!isset($documentType) || !in_array($documentType, [Document::TYPE_INVOICE, Document::TYPE_QUOTATION], true)) {
    throw new RuntimeException('Document type not configured.');
}

require_login();

$org = get_organization($_GET['id'] ?? 0);
if ($org === null) {
    header('Location: index.php?error=organization');
    exit;
}

$self       = basename($_SERVER['SCRIPT_NAME']) . '?id=' . (int) $_GET['id'];
$formAction = $self;
$errors     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doc = Document::fromRequest($documentType, $org, $settings, $_POST);

    if ($doc->isValid()) {
        require_once APP_ROOT . '/includes/pdf.php';

        $html = render_template('pdf/' . $documentType, [
            'doc'      => $doc,
            'org'      => $org,
            'settings' => $settings,
            'theme'    => $org['theme'],
            'logo'     => image_data_uri($org['logo']),
            'stamp'    => image_data_uri($org['stamp']),
        ]);

        $download = (($_POST['action'] ?? 'download') !== 'preview');

        stream_pdf($html, $doc->fileName(), $download, [
            'title'    => $doc->title() . ' ' . $doc->number . ' – ' . $org['name'],
            'author'   => $org['name'],
            'subject'  => $doc->typeLabel() . ' for ' . $doc->customer['name'],
            'keywords' => $doc->typeLabel() . ', ' . $org['name'] . ', ' . $doc->number,
            'footer'   => $org['name'] . ' · ' . $doc->title() . ' ' . $doc->number . ' · Generated on ' . date('d-M-Y H:i'),
        ]);
        // stream_pdf() exits.
    }

    $errors = $doc->errors;
} else {
    $doc = Document::defaults($documentType, $org, $settings);
}

$pageTitle = 'New ' . $doc->typeLabel() . ' – ' . $org['name'];

echo render_template('layout/header', ['pageTitle' => $pageTitle, 'settings' => $settings, 'org' => $org]);
echo render_template('forms/document_form', [
    'doc'        => $doc,
    'org'        => $org,
    'settings'   => $settings,
    'errors'     => $errors,
    'formAction' => $formAction,
]);
echo render_template('layout/footer', ['settings' => $settings, 'scripts' => ['assets/js/app.js?v=2']]);
