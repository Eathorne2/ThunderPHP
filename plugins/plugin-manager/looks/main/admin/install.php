<?php namespace PluginManager; if(!defined('ROOT')) exit('No direct script access allowed');
$inspection = get_value('plugin_manager_inspection'); $package = get_value('plugin_manager_temp_package'); $adminRoute = (get_value()['admin_route'] ?? 'admin');
?>
<div class="pm-wrap">
    <div class="pm-head"><div><h1>Install Plugin</h1><p>Upload a plugin ZIP. The package will be inspected before installation.</p></div><a class="pm-btn" href="<?=ROOT?>/<?=$adminRoute?>/plugins">Back</a></div>
    <?php if(empty($inspection)): ?>
        <form method="post" enctype="multipart/form-data" class="pm-panel pm-upload-form">
            <?=csrf()?>
            <input type="hidden" name="pm_action" value="upload_plugin">
            <label>Plugin ZIP Package</label>
            <input type="file" name="plugin_zip" accept=".zip" required>
            <button class="pm-btn pm-btn-primary">Inspect Package</button>
            <p class="pm-muted">Expected format: <code>plugin-id/config.json</code>, <code>plugin-id/plugin.php</code>, and plugin folders.</p>
        </form>
    <?php else: $json = $inspection['config']; ?>
        <div class="pm-panel">
            <h2>Dry Run Result</h2>
            <div class="pm-alert pm-alert-success"><?=e($inspection['message'])?></div>
            <table class="pm-table">
                <tr><th>Name</th><td><?=e($json->name ?? '')?></td></tr>
                <tr><th>ID</th><td><?=e($json->id ?? $inspection['root'])?></td></tr>
                <tr><th>Version</th><td><?=e($json->version ?? '')?></td></tr>
                <tr><th>Root Folder</th><td><?=e($inspection['root'])?></td></tr>
                <tr><th>Files</th><td><?=count($inspection['files'])?></td></tr>
            </table>
            <details><summary>Show files</summary><pre class="pm-readme"><?=e(implode("\n", $inspection['files']))?></pre></details>
            <form method="post" onsubmit="return confirm('Install or update this plugin and run migrations?');">
                <?=csrf()?>
                <input type="hidden" name="pm_action" value="confirm_install">
                <input type="hidden" name="package" value="<?=e($package)?>">
                <button class="pm-btn pm-btn-primary">Confirm Install / Update</button>
            </form>
        </div>
    <?php endif; ?>
</div>
