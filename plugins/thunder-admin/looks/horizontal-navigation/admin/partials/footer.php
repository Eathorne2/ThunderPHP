<?php

namespace ThunderAdmin;
?>
<footer class="ta-footer">
    <span class="ta-footer__copyright">&copy; <?= e(date('Y')) ?> <?= e(app_name()) ?></span>
    <div class="ta-footer__actions"><?php do_action('admin_footer'); ?></div>
</footer>
