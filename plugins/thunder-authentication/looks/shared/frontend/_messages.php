<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

$success = function_exists('message') ? \message('success', '', true) : null;
$fail = function_exists('message') ? \message('fail', '', true) : null;
$clean = static function (?string $text): string {
    if (!$text) return '';
    $token = '__TH_AUTH_BR__';
    $text = preg_replace('/<br\s*\/?>/i', $token, $text) ?? $text;
    $text = esc($text) ?? '';
    return str_replace($token, '<br>', $text);
};
?>
<?php if ($success): ?>
    <div class="th-auth-alert th-auth-alert--success"><?=$clean($success)?></div>
<?php endif; ?>
<?php if ($fail): ?>
    <div class="th-auth-alert th-auth-alert--danger"><?=$clean($fail)?></div>
<?php endif; ?>
