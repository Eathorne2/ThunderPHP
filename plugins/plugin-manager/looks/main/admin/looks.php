<?php namespace PluginManager; if(!defined('ROOT')) exit('No direct script access allowed');
$id = get_value('plugin_manager_plugin_id'); $json = get_value('plugin_manager_config'); $looks = get_value('plugin_manager_looks') ?? []; $adminRoute = (get_value()['admin_route'] ?? 'admin');
?>
<div class="pm-wrap">
    <div class="pm-head"><div><h1>Looks for <?=e($json->name ?? $id)?></h1><p>View, activate or delete installed looks.</p></div><a class="pm-btn" href="<?=ROOT?>/<?=$adminRoute?>/plugins/view/<?=e($id)?>">Back</a></div>
    <form method="post" enctype="multipart/form-data" class="pm-panel">
        <?=csrf()?>
        <input type="hidden" name="pm_action" value="upload_look">
        <input type="hidden" name="plugin_id" value="<?=e($id)?>">
        <h2>Install Look</h2>
        <input type="file" name="look_zip" accept=".zip" required>
        <button class="pm-btn pm-btn-primary">Upload Look ZIP</button>
        <p class="pm-muted">Expected format: <code>look-folder/look.json</code>.</p>
    </form>
    <div class="pm-grid">
        <?php foreach($looks as $look): ?>
            <div class="pm-card">
                <h3><?=e($look['name'])?></h3>
                <div class="pm-muted">Folder: <?=e($look['folder'])?> · v<?=e($look['version'])?></div>
                <p><?=e($look['description'])?></p>
                <?php if(($json->look ?? '') === $look['folder']): ?><span class="pm-badge pm-ok">Active</span><?php endif; ?>
                <table class="pm-table"><tr><th>Author</th><td><?=e($look['author'])?></td></tr><tr><th>Plugin Requires</th><td><?=e($look['plugin_requires'])?></td></tr><tr><th>Valid</th><td><?=$look['valid']?'Yes':'No'?></td></tr></table>
                <div class="pm-actions">
                    <form method="post"><?=csrf()?><input type="hidden" name="pm_action" value="set_active_look"><input type="hidden" name="plugin_id" value="<?=e($id)?>"><input type="hidden" name="look" value="<?=e($look['folder'])?>"><button class="pm-btn pm-btn-primary">Set Active</button></form>
                    <form method="post" onsubmit="return confirm('Delete this look? A plugin backup will be created first.');"><?=csrf()?><input type="hidden" name="pm_action" value="delete_look"><input type="hidden" name="plugin_id" value="<?=e($id)?>"><input type="hidden" name="look" value="<?=e($look['folder'])?>"><button class="pm-btn pm-btn-danger">Delete</button></form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
