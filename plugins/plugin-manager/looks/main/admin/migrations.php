<?php namespace PluginManager; if(!defined('ROOT')) exit('No direct script access allowed');
$items = get_value('plugin_manager_migrations') ?? [];
$id = get_value('plugin_manager_plugin_id') ?? '';
$adminRoute = (get_value()['admin_route'] ?? 'admin');
?>
<div class="pm-wrap">
    <div class="pm-head"><div><h1>Migration Status</h1><p>Compares plugin migration files with the <code>thunder_migrations</code> table.</p></div><a class="pm-btn" href="<?=ROOT?>/<?=$adminRoute?>/plugins">Back</a></div>
    <?php foreach($items as $pluginId => $item): $rows = $item['rows'] ?? []; $plugin = $item['plugin'] ?? []; ?>
        <div class="pm-panel">
            <div class="pm-section-head">
                <div><h2><?=e($plugin['name'] ?? $pluginId)?></h2><p class="pm-muted"><?=e($pluginId)?></p></div>
                <form method="post" onsubmit="return confirm('Run pending migrations for this plugin?');">
                    <?=csrf()?>
                    <input type="hidden" name="pm_action" value="run_migrations">
                    <input type="hidden" name="plugin_id" value="<?=e($pluginId)?>">
                    <button class="pm-btn pm-btn-primary">Run Migrations</button>
                </form>
            </div>
            <?php if(empty($rows)): ?>
                <p class="pm-muted">No migration files or records found.</p>
            <?php else: ?>
                <table class="pm-table">
                    <tr><th>Migration</th><th>Status</th><th>Batch</th><th>Ran At</th><th>Checksum</th></tr>
                    <?php foreach($rows as $row): ?>
                        <tr>
                            <td><?=e($row['name'])?></td>
                            <td><span class="pm-badge <?=($row['status']==='ran')?'pm-ok':(($row['status']==='pending')?'pm-warn':'pm-off')?>"><?=e($row['status'])?></span></td>
                            <td><?=e($row['batch'])?></td>
                            <td><?=e($row['ran_at'])?></td>
                            <td><code title="File: <?=e($row['file_checksum'])?> DB: <?=e($row['db_checksum'])?>"><?=e(substr($row['file_checksum'] ?: $row['db_checksum'], 0, 12))?></code></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
