<?php

namespace ThunderAdmin;

$success = function_exists('message') ? \message('success', '', true) : null;
$fail = function_exists('message') ? \message('fail', '', true) : null;
?>
<?php if ($success): ?>
    <div class="ta-alert ta-alert--success" role="status">
        <i class="ta-alert__icon fa-solid fa-circle-check" aria-hidden="true"></i>
        <span class="ta-alert__message"><?= flash_message_html($success) ?></span>
    </div>
<?php endif; ?>

<?php if ($fail): ?>
    <div class="ta-alert ta-alert--danger" role="alert">
        <i class="ta-alert__icon fa-solid fa-circle-exclamation" aria-hidden="true"></i>
        <span class="ta-alert__message"><?= flash_message_html($fail) ?></span>
    </div>
<?php endif; ?>
