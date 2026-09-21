<?php
/**
 * Page shell (bottom). Variables: $settings, $scripts (optional array of script urls)
 */
$scripts = $scripts ?? [];
?>
</main>

<footer class="docs-footer">
    <div class="container-xxl d-flex flex-column flex-md-row justify-content-between gap-1">
        <span>&copy; <?= date('Y') ?> Oshin Services &amp; Friends Enterprises, Nagpur. All rights reserved.</span>
        <span class="text-muted">Documents are generated on demand and downloaded to your computer &mdash; nothing is stored on the server.</span>
    </div>
</footer>

<?php foreach ($scripts as $script): ?>
    <script src="<?= e($script) ?>"></script>
<?php endforeach; ?>
</body>
</html>
