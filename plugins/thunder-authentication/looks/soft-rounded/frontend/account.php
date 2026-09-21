<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');
?>
<link rel="stylesheet" href="<?=esc(auth_asset_url('css/auth.css'))?>">
<style><?=auth_palette_css()?></style>
<?php require plugin_path('looks/shared/frontend/account.php'); ?>
<script src="<?=esc(auth_asset_url('js/auth.js'))?>"></script>
