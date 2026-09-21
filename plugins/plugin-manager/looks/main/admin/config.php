<?php namespace PluginManager; if(!defined('ROOT')) exit('No direct script access allowed');
$id = get_value('plugin_manager_plugin_id'); $raw = get_value('plugin_manager_config_raw') ?? ''; $adminRoute = (get_value()['admin_route'] ?? 'admin');
?>
<div class="pm-wrap">
    <div class="pm-head"><div><h1>Edit config.json</h1><p>Validate JSON before saving. A backup ZIP is created automatically.</p></div><a class="pm-btn" href="<?=ROOT?>/<?=$adminRoute?>/plugins/view/<?=e($id)?>">Back</a></div>
    <form method="post" class="pm-panel" id="pm-config-form">
        <?=csrf()?>
        <input type="hidden" name="pm_action" value="save_config">
        <input type="hidden" name="plugin_id" value="<?=e($id)?>">
        <textarea name="config_json" id="pm-config-json" class="pm-codearea" spellcheck="false"><?=e($raw)?></textarea>
        <div class="pm-config-result" id="pm-config-result"></div>
        <button type="button" class="pm-btn" id="pm-validate-json">Validate JSON</button>
        <button class="pm-btn pm-btn-primary">Save Config</button>
    </form>
</div>
