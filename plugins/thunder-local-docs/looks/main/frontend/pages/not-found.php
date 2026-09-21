<?php namespace ThunderLocalDocs; ?>
<div class="docs-empty docs-not-found">
    <span class="docs-error-code">404</span>
    <h1><?= docs_escape($page['title'] ?? 'Documentation page not found') ?></h1>
    <p><?= docs_escape($page['message'] ?? 'The requested documentation page could not be found.') ?></p>
    <a class="docs-button" href="<?= docs_escape(docs_url()) ?>">Return to documentation</a>
</div>
