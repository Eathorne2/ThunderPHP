<?php namespace PluginManager; if(!defined('ROOT')) exit('No direct script access allowed');
$id = get_value('plugin_manager_plugin_id');
$json = get_value('plugin_manager_config');
$readme = get_value('plugin_manager_readme') ?? '';
$readmeHtml = get_value('plugin_manager_readme_html') ?? '';
$adminRoute = (get_value()['admin_route'] ?? 'admin');
$deps = dependencies($json);
$routes = routes($json);
$perms = permissions($json);
$summary = plugin_summary($id, $json);
$migrationRows = get_value('plugin_manager_migration_status') ?? [];
$pending = count(array_filter($migrationRows, static fn(array $row): bool => ($row['status'] ?? '') === 'pending'));
$changed = count(array_filter($migrationRows, static fn(array $row): bool => ($row['status'] ?? '') === 'changed'));
$website = trim((string)($json->website ?? ''));
$websiteHref = $website !== '' && !preg_match('#^https?://#i', $website) ? 'https://' . $website : $website;
?>
<div class="pm-wrap">
    <section class="pm-detail-hero pm-reveal">
        <a class="pm-back-link" href="<?=ROOT?>/<?=$adminRoute?>/plugins"><i class="fa-solid fa-arrow-left"></i> All plugins</a>
        <div class="pm-detail-hero__main">
            <div class="pm-detail-thumb">
                <?php if(!empty($summary['thumbnail'])): ?>
                    <img src="<?=e($summary['thumbnail'])?>" alt="<?=e($json->name ?? $id)?> thumbnail">
                <?php else: ?>
                    <i class="fa-solid fa-plug"></i>
                <?php endif; ?>
            </div>
            <div class="pm-detail-copy">
                <div class="pm-detail-meta">
                    <span class="pm-badge <?=!empty($json->active) ? 'pm-ok' : 'pm-off'?>"><i class="fa-solid <?=!empty($json->active) ? 'fa-circle-check' : 'fa-circle-pause'?>"></i> <?=!empty($json->active) ? 'Active' : 'Disabled'?></span>
                    <code><?=e($id)?></code>
                    <span>v<?=e($json->version ?? '—')?></span>
                </div>
                <h1><?=e($json->name ?? $id)?></h1>
                <p><?=e($json->description ?? 'No description has been provided for this plugin.')?></p>
                <div class="pm-detail-byline">
                    <?php if(!empty($json->author)): ?><span><i class="fa-solid fa-user-pen"></i> <?=e($json->author)?></span><?php endif; ?>
                    <?php if($websiteHref !== ''): ?><a href="<?=e($websiteHref)?>" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-arrow-up-right-from-square"></i> <?=e($website)?></a><?php endif; ?>
                </div>
            </div>
            <div class="pm-detail-actions">
                <a class="pm-btn pm-btn-soft" href="<?=ROOT?>/<?=$adminRoute?>/plugins/config/<?=e($id)?>"><i class="fa-solid fa-code"></i> Edit config</a>
                <a class="pm-btn pm-btn-soft" href="<?=ROOT?>/<?=$adminRoute?>/plugins/looks/<?=e($id)?>"><i class="fa-solid fa-palette"></i> Looks</a>
                <a class="pm-btn pm-btn-primary" href="<?=ROOT?>/<?=$adminRoute?>/plugins/export/<?=e($id)?>"><i class="fa-solid fa-file-export"></i> Export ZIP</a>
            </div>
        </div>
    </section>

    <nav class="pm-anchor-nav pm-reveal" aria-label="Plugin details sections">
        <a href="#pm-overview">Overview</a>
        <a href="#pm-dependencies">Dependencies <span><?=count($deps)?></span></a>
        <a href="#pm-routes">Routes <span><?=count($routes)?></span></a>
        <a href="#pm-permissions">Permissions <span><?=count($perms)?></span></a>
        <a href="#pm-migrations">Migrations <span><?=count($migrationRows)?></span></a>
        <a href="#pm-readme">README</a>
    </nav>

    <section class="pm-fact-grid pm-reveal" aria-label="Plugin facts">
        <div class="pm-fact"><i class="fa-solid fa-code-branch"></i><div><span>Core requires</span><strong><?=e($json->core_requires ?? 'Not specified')?></strong></div></div>
        <div class="pm-fact"><i class="fa-solid fa-layer-group"></i><div><span>Load priority</span><strong><?=e($json->index ?? 100)?></strong></div></div>
        <div class="pm-fact"><i class="fa-solid fa-wand-magic-sparkles"></i><div><span>Active look</span><strong><?=e($json->look ?? 'None')?></strong></div></div>
        <div class="pm-fact"><i class="fa-solid fa-file-lines"></i><div><span>Documentation</span><strong><?=$readme !== '' ? 'README available' : 'Not provided'?></strong></div></div>
    </section>

    <section id="pm-overview" class="pm-two pm-reveal">
        <article class="pm-panel">
            <div class="pm-panel-heading">
                <span class="pm-panel-icon"><i class="fa-solid fa-circle-info"></i></span>
                <div><h2>Plugin information</h2><p>Metadata read directly from <code>config.json</code>.</p></div>
            </div>
            <dl class="pm-data-list">
                <div><dt>Plugin ID</dt><dd><code><?=e($id)?></code></dd></div>
                <div><dt>Version</dt><dd><?=e($json->version ?? '—')?></dd></div>
                <div><dt>Author</dt><dd><?=e($json->author ?? '—')?></dd></div>
                <div><dt>Type</dt><dd><?=e($json->type ?? 'Not specified')?></dd></div>
                <div><dt>Active look</dt><dd><?=e($json->look ?? '—')?></dd></div>
                <div><dt>Entrypoint</dt><dd><?=$summary['has_plugin_php'] ? '<span class="pm-good-text">plugin.php found</span>' : '<span class="pm-bad-text">plugin.php missing</span>'?></dd></div>
            </dl>
        </article>

        <article class="pm-panel">
            <div class="pm-panel-heading">
                <span class="pm-panel-icon <?=empty($summary['health']) ? 'pm-panel-icon--success' : 'pm-panel-icon--warning'?>"><i class="fa-solid fa-heart-pulse"></i></span>
                <div><h2>Health report</h2><p>Basic structure, look and dependency checks.</p></div>
            </div>
            <?php if(empty($summary['health'])): ?>
                <div class="pm-health-state pm-health-state--good"><i class="fa-solid fa-shield-check"></i><div><strong>No obvious issues found</strong><span>The plugin passed all available static health checks.</span></div></div>
            <?php else: ?>
                <ul class="pm-issue-list">
                    <?php foreach($summary['health'] as $issue): ?><li><i class="fa-solid fa-triangle-exclamation"></i> <?=e($issue)?></li><?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>
    </section>

    <section id="pm-dependencies" class="pm-panel pm-reveal">
        <div class="pm-section-head">
            <div class="pm-panel-heading">
                <span class="pm-panel-icon"><i class="fa-solid fa-link"></i></span>
                <div><h2>Dependencies</h2><p>Plugins this package expects or can optionally extend.</p></div>
            </div>
            <span class="pm-count-pill"><?=count($deps)?> total</span>
        </div>
        <?php if(empty($deps)): ?>
            <div class="pm-inline-empty"><i class="fa-solid fa-link-slash"></i> No dependencies listed.</div>
        <?php else: ?>
            <div class="pm-table-wrap"><table class="pm-table">
                <thead><tr><th>Name</th><th>ID</th><th>Version</th><th>Requirement</th><th>Status</th></tr></thead>
                <tbody><?php foreach($deps as $dep): ?><tr>
                    <td><strong><?=e($dep['name'])?></strong></td>
                    <td><code><?=e($dep['id'])?></code></td>
                    <td><?=e($dep['version'] ?: 'Any')?></td>
                    <td><?=$dep['required'] ? 'Required' : 'Optional'?></td>
                    <td><span class="pm-badge <?=$dep['installed'] ? 'pm-ok' : ($dep['required'] ? 'pm-warn' : 'pm-off')?>"><i class="fa-solid <?=$dep['installed'] ? 'fa-circle-check' : 'fa-circle-xmark'?>"></i> <?=$dep['installed'] ? 'Installed' : 'Missing'?></span></td>
                </tr><?php endforeach; ?></tbody>
            </table></div>
        <?php endif; ?>
    </section>

    <div class="pm-two">
        <section id="pm-routes" class="pm-panel pm-reveal">
            <div class="pm-section-head">
                <div class="pm-panel-heading"><span class="pm-panel-icon"><i class="fa-solid fa-route"></i></span><div><h2>Routes</h2><p>Named routes declared by this plugin.</p></div></div>
                <span class="pm-count-pill"><?=count($routes)?></span>
            </div>
            <?php if(empty($routes)): ?><div class="pm-inline-empty"><i class="fa-solid fa-road-barrier"></i> No named routes declared.</div><?php else: ?>
                <div class="pm-table-wrap"><table class="pm-table"><thead><tr><th>Method</th><th>Pattern</th><th>Name</th></tr></thead><tbody>
                <?php foreach($routes as $route): ?><tr><td><span class="pm-method"><?=e(strtoupper($route->method ?? 'GET'))?></span></td><td><code><?=e($route->pattern ?? '')?></code></td><td><?=e($route->name ?? '')?></td></tr><?php endforeach; ?>
                </tbody></table></div>
            <?php endif; ?>
        </section>

        <section id="pm-permissions" class="pm-panel pm-reveal">
            <div class="pm-section-head">
                <div class="pm-panel-heading"><span class="pm-panel-icon"><i class="fa-solid fa-key"></i></span><div><h2>Permissions</h2><p>Capabilities registered by the plugin.</p></div></div>
                <span class="pm-count-pill"><?=count($perms)?></span>
            </div>
            <?php if(empty($perms)): ?><div class="pm-inline-empty"><i class="fa-solid fa-key"></i> No permissions declared.</div><?php else: ?>
                <div class="pm-table-wrap"><table class="pm-table"><thead><tr><th>Name</th><th>Slug</th><th>Group</th></tr></thead><tbody>
                <?php foreach($perms as $perm): ?><tr><td><strong><?=e($perm->name ?? '')?></strong></td><td><code><?=e($perm->slug ?? '')?></code></td><td><?=e($perm->group ?? '')?></td></tr><?php endforeach; ?>
                </tbody></table></div>
            <?php endif; ?>
        </section>
    </div>

    <section id="pm-migrations" class="pm-panel pm-reveal">
        <div class="pm-section-head">
            <div class="pm-panel-heading"><span class="pm-panel-icon"><i class="fa-solid fa-database"></i></span><div><h2>Migrations</h2><p>Status compared with the <code>thunder_migrations</code> table.</p></div></div>
            <div class="pm-section-actions">
                <?php if($pending > 0): ?><span class="pm-badge pm-warn"><?=$pending?> pending</span><?php endif; ?>
                <?php if($changed > 0): ?><span class="pm-badge pm-warn"><?=$changed?> changed</span><?php endif; ?>
                <a class="pm-btn pm-btn-soft" href="<?=ROOT?>/<?=$adminRoute?>/plugins/migrations/<?=e($id)?>">Full status</a>
            </div>
        </div>
        <?php if(empty($migrationRows)): ?>
            <div class="pm-inline-empty"><i class="fa-solid fa-database"></i> No migration files or records found.</div>
        <?php else: ?>
            <div class="pm-table-wrap"><table class="pm-table"><thead><tr><th>Migration</th><th>Status</th><th>Batch</th><th>Ran at</th></tr></thead><tbody>
            <?php foreach($migrationRows as $row): ?><tr>
                <td><code><?=e($row['name'])?></code></td>
                <td><span class="pm-badge <?=($row['status']==='ran') ? 'pm-ok' : (($row['status']==='pending') ? 'pm-warn' : 'pm-off')?>"><?=e($row['status'])?></span></td>
                <td><?=e($row['batch'] ?: '—')?></td><td><?=e($row['ran_at'] ?: '—')?></td>
            </tr><?php endforeach; ?>
            </tbody></table></div>
        <?php endif; ?>
        <form method="post" class="pm-panel-footer" onsubmit="return confirm('Run migrations for this plugin?');">
            <?=csrf()?><input type="hidden" name="pm_action" value="run_migrations"><input type="hidden" name="plugin_id" value="<?=e($id)?>">
            <button class="pm-btn pm-btn-primary"><i class="fa-solid fa-play"></i> Run migrations</button>
        </form>
    </section>

    <section id="pm-readme" class="pm-panel pm-readme-panel pm-reveal">
        <div class="pm-section-head">
            <div class="pm-panel-heading"><span class="pm-panel-icon"><i class="fa-brands fa-markdown"></i></span><div><h2>README</h2><p>Rendered from the plugin's <code>README.md</code> file.</p></div></div>
            <?php if($readme !== ''): ?><button class="pm-btn pm-btn-soft" type="button" data-pm-copy-readme><i class="fa-regular fa-copy"></i> Copy Markdown</button><?php endif; ?>
        </div>
        <?php if($readmeHtml !== ''): ?>
            <article class="pm-markdown"><?=$readmeHtml?></article>
            <textarea id="pm-readme-source" hidden><?=e($readme)?></textarea>
        <?php else: ?>
            <div class="pm-empty pm-empty--compact"><span class="pm-empty__icon"><i class="fa-solid fa-file-circle-xmark"></i></span><h3>No README.md found</h3><p>Add documentation to the plugin root to display it here.</p></div>
        <?php endif; ?>
    </section>

    <details class="pm-danger-zone pm-panel pm-reveal">
        <summary><span><i class="fa-solid fa-triangle-exclamation"></i> Danger zone</span><i class="fa-solid fa-chevron-down"></i></summary>
        <div class="pm-danger-content">
            <div><strong>Delete this plugin</strong><p>A backup ZIP will be created first, but the installed plugin folder will be removed.</p></div>
            <form method="post" onsubmit="return confirm('Delete this whole plugin? A backup ZIP will be created first.');">
                <?=csrf()?><input type="hidden" name="pm_action" value="delete_plugin"><input type="hidden" name="plugin_id" value="<?=e($id)?>">
                <button class="pm-btn pm-btn-danger"><i class="fa-solid fa-trash"></i> Delete plugin</button>
            </form>
        </div>
    </details>
</div>
