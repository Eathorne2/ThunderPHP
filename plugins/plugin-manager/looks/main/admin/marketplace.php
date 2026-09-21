<?php namespace PluginManager; if(!defined('ROOT')) exit('No direct script access allowed');
$market = get_value('plugin_manager_marketplace') ?? ['ok'=>false,'message'=>'Marketplace not loaded.','plugins'=>[]];
$url = get_value('plugin_manager_marketplace_url') ?? '';
$adminRoute = (get_value()['admin_route'] ?? 'admin');
?>
<div class="pm-wrap">
    <div class="pm-head">
        <div><h1>Marketplace</h1><p>Browse remote ThunderPHP plugins and install or update them directly.</p></div>
        <div class="pm-actions"><a class="pm-btn" href="<?=ROOT?>/<?=$adminRoute?>/plugins/marketplace-settings">Settings</a><a class="pm-btn" href="<?=ROOT?>/<?=$adminRoute?>/plugins">Back</a></div>
    </div>

    <?php if(empty($url)): ?>
        <div class="pm-alert pm-alert-warning">No marketplace URL is configured yet. <a href="<?=ROOT?>/<?=$adminRoute?>/plugins/marketplace-settings">Add one now</a>.</div>
    <?php elseif(empty($market['ok'])): ?>
        <div class="pm-alert pm-alert-warning"><?=e($market['message'] ?? 'Could not load marketplace.')?></div>
    <?php else: ?>
        <div class="pm-alert pm-alert-success"><?=e($market['data']->marketplace ?? 'Marketplace')?> loaded from <code><?=e($url)?></code>.</div>
        <div class="pm-grid">
            <?php foreach(($market['plugins'] ?? []) as $remote): $status = marketplace_plugin_status($remote); ?>
                <div class="pm-card">
                    <div class="pm-card-top">
                        <div class="pm-thumb">
                            <?php if(!empty($remote->thumbnail)): ?><img src="<?=e($remote->thumbnail)?>" alt=""><?php else: ?><i class="fa-solid fa-store"></i><?php endif; ?>
                        </div>
                        <div>
                            <h3><?=e($remote->name ?? $status['id'])?></h3>
                            <div class="pm-muted"><?=e($status['id'])?> · v<?=e($status['remote_version'])?></div>
                        </div>
                        <?php if($status['update_available']): ?><span class="pm-badge pm-warn">Update</span><?php elseif($status['installed']): ?><span class="pm-badge pm-ok">Installed</span><?php else: ?><span class="pm-badge">Remote</span><?php endif; ?>
                    </div>
                    <p><?=e($remote->description ?? '')?></p>
                    <div class="pm-stats"><span><?=e($remote->category ?? 'Uncategorized')?></span><span><?=e($remote->type ?? 'free')?></span><span><?=!empty($remote->price) ? e($remote->price) : 'Free'?></span></div>

                    <?php if(!empty($status['dependencies'])): ?>
                        <div class="pm-deps-mini">
                            <?php foreach($status['dependencies'] as $dep): ?>
                                <span class="pm-dep <?=$dep['installed'] ? 'pm-ok-text' : ($dep['required'] ? 'pm-bad-text' : 'pm-muted')?>"><?=e($dep['id'])?> <?=$dep['installed'] ? '✓' : 'missing'?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($status['local_version'])): ?><p class="pm-muted">Installed version: <?=e($status['local_version'])?></p><?php endif; ?>
                    <?php if(!empty($remote->readme)): ?><a class="pm-link" href="<?=e($remote->readme)?>" target="_blank" rel="noopener">README</a><?php endif; ?>

                    <form method="post" onsubmit="return confirm('Download and install this marketplace package? A backup will be created before updating an installed plugin.');">
                        <?=csrf()?>
                        <input type="hidden" name="pm_action" value="marketplace_install">
                        <input type="hidden" name="plugin_id" value="<?=e($status['id'])?>">
                        <button class="pm-btn pm-btn-primary" <?=!empty($status['missing_required']) ? 'disabled' : ''?>><?=$status['installed'] ? ($status['update_available'] ? 'Update' : 'Reinstall') : 'Install'?></button>
                    </form>
                    <?php if(!empty($status['missing_required'])): ?><div class="pm-mini-warning">Required dependencies missing. Install those first.</div><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
