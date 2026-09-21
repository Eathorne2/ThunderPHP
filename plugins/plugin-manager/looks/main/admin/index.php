<?php namespace PluginManager; if(!defined('ROOT')) exit('No direct script access allowed');
$plugins = get_value('plugin_manager_plugins') ?? [];
$missing = get_value('plugin_manager_missing') ?? [];
$outdated = get_value('plugin_manager_outdated') ?? [];
$migrations = get_value('plugin_manager_migrations') ?? [];
$adminRoute = (get_value()['admin_route'] ?? 'admin');
$total = count($plugins);
$active = count(array_filter($plugins, static fn(array $plugin): bool => !empty($plugin['active'])));
$disabled = $total - $active;
$healthIssues = array_sum(array_map(static fn(array $plugin): int => count($plugin['health'] ?? []), $plugins));
$pendingMigrations = array_sum(array_map(static fn(array $item): int => (int)($item['pending'] ?? 0), $migrations));
?>
<div class="pm-wrap">
    <section class="pm-hero pm-reveal">
        <div class="pm-hero__content">
            <span class="pm-eyebrow"><i class="fa-solid fa-cubes"></i> ThunderPHP extensions</span>
            <h1>Plugin Manager</h1>
            <p>Install, inspect and maintain every plugin from one clear workspace.</p>
        </div>
        <div class="pm-hero__actions">
            <a class="pm-btn pm-btn-soft" href="<?=ROOT?>/<?=$adminRoute?>/plugins/marketplace"><i class="fa-solid fa-store"></i> Marketplace</a>
            <a class="pm-btn pm-btn-soft" href="<?=ROOT?>/<?=$adminRoute?>/plugins/migrations"><i class="fa-solid fa-database"></i> Migrations</a>
            <a class="pm-btn pm-btn-primary" href="<?=ROOT?>/<?=$adminRoute?>/plugins/install"><i class="fa-solid fa-file-arrow-up"></i> Install Plugin</a>
        </div>
    </section>

    <section class="pm-overview pm-reveal" aria-label="Plugin overview">
        <div class="pm-overview-card">
            <span class="pm-overview-icon"><i class="fa-solid fa-layer-group"></i></span>
            <div><strong><?=$total?></strong><span>Installed plugins</span></div>
        </div>
        <div class="pm-overview-card">
            <span class="pm-overview-icon pm-overview-icon--success"><i class="fa-solid fa-circle-check"></i></span>
            <div><strong><?=$active?></strong><span>Active</span></div>
        </div>
        <div class="pm-overview-card">
            <span class="pm-overview-icon pm-overview-icon--muted"><i class="fa-solid fa-circle-pause"></i></span>
            <div><strong><?=$disabled?></strong><span>Disabled</span></div>
        </div>
        <div class="pm-overview-card">
            <span class="pm-overview-icon <?=($healthIssues + $pendingMigrations) > 0 ? 'pm-overview-icon--warning' : 'pm-overview-icon--success'?>"><i class="fa-solid fa-stethoscope"></i></span>
            <div><strong><?=$healthIssues + $pendingMigrations?></strong><span>Items needing attention</span></div>
        </div>
    </section>

    <?php if(!empty($missing) || !empty($outdated)): ?>
        <div class="pm-alert pm-alert-warning pm-alert-row pm-reveal">
            <span class="pm-alert-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <div><strong>Attention needed</strong><p><?=count($missing)?> missing dependencies and <?=count($outdated)?> core compatibility warnings were found.</p></div>
            <a class="pm-alert-link" href="<?=ROOT?>/<?=$adminRoute?>/plugins/dependencies">Review issues <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    <?php endif; ?>

    <section class="pm-toolbar pm-reveal">
        <label class="pm-search" for="pm-plugin-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input id="pm-plugin-search" type="search" placeholder="Search plugins by name, ID or author" autocomplete="off">
            <kbd>/</kbd>
        </label>
        <div class="pm-filter-group" aria-label="Filter plugins">
            <button class="pm-filter is-active" type="button" data-pm-filter="all">All <span><?=$total?></span></button>
            <button class="pm-filter" type="button" data-pm-filter="active">Active <span><?=$active?></span></button>
            <button class="pm-filter" type="button" data-pm-filter="disabled">Disabled <span><?=$disabled?></span></button>
            <button class="pm-filter" type="button" data-pm-filter="issues">Issues <span><?=count(array_filter($plugins, static fn(array $plugin): bool => !empty($plugin['health'])))?></span></button>
        </div>
    </section>

    <?php if(empty($plugins)): ?>
        <section class="pm-empty pm-panel pm-reveal">
            <span class="pm-empty__icon"><i class="fa-solid fa-plug-circle-plus"></i></span>
            <h2>No plugins installed</h2>
            <p>Install a ZIP package or browse the marketplace to get started.</p>
            <a class="pm-btn pm-btn-primary" href="<?=ROOT?>/<?=$adminRoute?>/plugins/install">Install your first plugin</a>
        </section>
    <?php else: ?>
        <div class="pm-grid" id="pm-plugin-grid">
            <?php foreach($plugins as $index => $plugin):
                $detailsUrl = ROOT . '/' . $adminRoute . '/plugins/view/' . rawurlencode($plugin['id']);
                $searchText = strtolower(implode(' ', [$plugin['name'], $plugin['id'], $plugin['author'], $plugin['description']]));
                $status = $plugin['active'] ? 'active' : 'disabled';
                $hasIssues = !empty($plugin['health']);
            ?>
                <article
                    class="pm-card pm-plugin-card pm-reveal"
                    style="--pm-delay: <?=min($index, 10) * 45?>ms"
                    role="link"
                    tabindex="0"
                    data-pm-href="<?=e($detailsUrl)?>"
                    data-pm-search="<?=e($searchText)?>"
                    data-pm-status="<?=$status?>"
                    data-pm-health="<?=$hasIssues ? 'issues' : 'healthy'?>"
                    aria-label="View <?=e($plugin['name'])?> details"
                >
                    <div class="pm-card-top">
                        <div class="pm-thumb">
                            <?php if(!empty($plugin['thumbnail'])): ?>
                                <img src="<?=e($plugin['thumbnail'])?>" alt="<?=e($plugin['name'])?> thumbnail" loading="lazy">
                            <?php else: ?>
                                <i class="fa-solid fa-plug"></i>
                            <?php endif; ?>
                        </div>
                        <div class="pm-card-title">
                            <h2><?=e($plugin['name'])?></h2>
                            <div class="pm-muted"><code><?=e($plugin['id'])?></code> <span>v<?=e($plugin['version'] ?: '—')?></span></div>
                        </div>
                        <span class="pm-badge <?=$plugin['active'] ? 'pm-ok' : 'pm-off'?>"><i class="fa-solid <?=$plugin['active'] ? 'fa-circle-check' : 'fa-circle-pause'?>"></i> <?=$plugin['active'] ? 'Active' : 'Disabled'?></span>
                    </div>

                    <p class="pm-card-description"><?=e($plugin['description'] ?: 'No plugin description has been provided.')?></p>

                    <?php if($hasIssues): ?>
                        <div class="pm-mini-warning"><i class="fa-solid fa-triangle-exclamation"></i> <?=count($plugin['health'])?> health issue<?=count($plugin['health']) === 1 ? '' : 's'?></div>
                    <?php else: ?>
                        <div class="pm-mini-success"><i class="fa-solid fa-shield-check"></i> Health checks passed</div>
                    <?php endif; ?>

                    <div class="pm-stats" aria-label="Plugin statistics">
                        <span><i class="fa-solid fa-route"></i> <?=$plugin['routes_count']?> routes</span>
                        <span><i class="fa-solid fa-key"></i> <?=$plugin['permissions_count']?> permissions</span>
                        <span><i class="fa-solid fa-palette"></i> <?=$plugin['looks_count']?> looks</span>
                    </div>

                    <div class="pm-card-controls">
                        <form method="post" class="pm-priority-form" aria-label="Update <?=e($plugin['name'])?> priority">
                            <?=csrf()?>
                            <input type="hidden" name="pm_action" value="save_index">
                            <input type="hidden" name="plugin_id" value="<?=e($plugin['id'])?>">
                            <label for="pm-index-<?=e($plugin['id'])?>">Load priority</label>
                            <div class="pm-priority-input">
                                <input id="pm-index-<?=e($plugin['id'])?>" type="number" name="index" value="<?=e($plugin['index'])?>" min="0" step="1">
                                <button class="pm-icon-btn" type="submit" title="Save priority" aria-label="Save priority"><i class="fa-solid fa-check"></i></button>
                            </div>
                        </form>
                        <div class="pm-card-quick-actions">
                            <a class="pm-icon-btn" href="<?=ROOT?>/<?=$adminRoute?>/plugins/config/<?=e($plugin['id'])?>" title="Edit config" aria-label="Edit config"><i class="fa-solid fa-code"></i></a>
                            <a class="pm-icon-btn" href="<?=ROOT?>/<?=$adminRoute?>/plugins/migrations/<?=e($plugin['id'])?>" title="View migrations" aria-label="View migrations"><i class="fa-solid fa-database"></i></a>
                            <form method="post">
                                <?=csrf()?>
                                <input type="hidden" name="pm_action" value="toggle_active">
                                <input type="hidden" name="plugin_id" value="<?=e($plugin['id'])?>">
                                <button class="pm-icon-btn" type="submit" title="<?=$plugin['active'] ? 'Disable' : 'Enable'?> plugin" aria-label="<?=$plugin['active'] ? 'Disable' : 'Enable'?> plugin"><i class="fa-solid <?=$plugin['active'] ? 'fa-power-off' : 'fa-play'?>"></i></button>
                            </form>
                        </div>
                    </div>

                    <div class="pm-card-link">View plugin details <i class="fa-solid fa-arrow-right"></i></div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="pm-empty pm-panel" id="pm-no-results" hidden>
            <span class="pm-empty__icon"><i class="fa-solid fa-magnifying-glass"></i></span>
            <h2>No matching plugins</h2>
            <p>Try another search term or clear the selected filter.</p>
        </div>
    <?php endif; ?>
</div>
