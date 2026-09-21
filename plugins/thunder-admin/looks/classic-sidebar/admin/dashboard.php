<?php

namespace ThunderAdmin;

ob_start();
do_action('admin_dashboard_widgets');
$widgets = trim((string) ob_get_clean());
?>
<div class="ta-dashboard">
    <?php if ($widgets !== ''): ?>
        <div class="ta-dashboard__grid"><?= $widgets ?></div>
    <?php else: ?>
        <section class="ta-empty-state">
            <div class="ta-empty-state__icon"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></div>
            <h2 class="ta-empty-state__title">No dashboard widgets yet.</h2>
            <p class="ta-empty-state__text">Plugins can register widgets using <code class="ta-code">admin_dashboard_widgets</code>.</p>
        </section>
    <?php endif; ?>
</div>
