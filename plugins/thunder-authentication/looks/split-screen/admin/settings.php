<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');
?>
<link rel="stylesheet" href="<?=esc(auth_asset_url('css/admin.css'))?>">
<style><?=auth_palette_css()?></style>
<?php require plugin_path('looks/shared/admin/settings.php'); ?>
<script src="<?=esc(auth_asset_url('js/admin.js'))?>"></script>
