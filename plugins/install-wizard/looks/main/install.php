<?php
/** @var int $step */
/** @var array $flash */
/** @var string $token */
/** @var string $detected_environment */
/** @var bool $root_writable */
/** @var bool $sample_exists */
/** @var string $logo_url */
/** @var array $migration_result */

use function InstallWizard\old;
use function InstallWizard\root_path;
use function InstallWizard\config_path;
use function InstallWizard\sample_config_path;

$environment = old('environment', $detected_environment);
$default_root = old('root_url', '');
$default_port = old('db_port', '3306');
$default_driver = old('db_driver', 'mysql');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Install ThunderPHP</title>
    <link rel="stylesheet" href="<?= current_look_http('assets/css/install.css') ?>">
</head>
<body class="iw-body">
    <main class="iw-shell">
        <section class="iw-panel">
            <aside class="iw-side">
                <div class="iw-brand">
                    <?php if(!empty($logo_url)): ?>
                        <img src="<?=($logo_url) ?>" alt="Current logo" class="iw-brand-logo">
                    <?php else: ?>
                        <div class="iw-brand-mark">T</div>
                    <?php endif; ?>
                    <div>
                        <div class="iw-kicker">ThunderPHP</div>
                        <h1>Installation Wizard</h1>
                    </div>
                </div>

                <ol class="iw-steps">
                    <li class="<?= $step === 1 ? 'active' : ($step > 1 ? 'done' : '') ?>"><span>1</span> Requirements</li>
                    <li class="<?= $step === 2 ? 'active' : ($step > 2 ? 'done' : '') ?>"><span>2</span> Database</li>
                    <li class="<?= $step === 3 ? 'active' : ($step > 3 ? 'done' : '') ?>"><span>3</span> App Details</li>
                    <li class="<?= $step === 4 ? 'active' : ($step > 4 ? 'done' : '') ?>"><span>4</span> Migrations</li>
                    <li class="<?= $step === 5 ? 'active' : '' ?>"><span>5</span> Complete</li>
                </ol>

                <div class="iw-note">
                    You can edit these settings later from the admin area.
                </div>
            </aside>

            <section class="iw-content">
                <?php if(!empty($flash)): ?>
                    <div class="iw-flash-wrap">
                        <?php foreach($flash as $item): ?>
                            <div class="iw-flash iw-flash-<?= htmlspecialchars($item['type']) ?>">
                                <?= htmlspecialchars($item['message']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if($step === 1): ?>
                    <header class="iw-head">
                        <div class="iw-pill">Step 1 of 5</div>
                        <h2>Welcome. Let’s create your config file.</h2>
                        <p>This wizard appears because <code>config.php</code> does not exist. It will collect your database and app details, then create <code>config.php</code> from <code>config-sample.php</code>. You can do this manually if this wizard fails for some reason.</p>
                    </header>

                    <div class="iw-grid two">
                        <div class="iw-card">
                            <h3>You will need</h3>
                            <ul class="iw-list">
                                <li>Database name, username, password & host name</li>
                                <li>Whether this is a local or online server</li>
                                <li>Application name and description</li>
                                <li>An optional logo image</li>
                            </ul>
                        </div>
                        <div class="iw-card">
                            <h3>System checks</h3>
                            <div class="iw-check <?= $sample_exists ? 'ok' : 'bad' ?>">
                                <span></span> config-sample.php <?= $sample_exists ? 'found' : 'missing' ?>
                            </div>
                            <div class="iw-check <?= $root_writable ? 'ok' : 'bad' ?>">
                                <span></span> Main folder <?= $root_writable ? 'is writable' : 'is not writable' ?>
                            </div>
                            <p class="iw-small">Root: <code><?= htmlspecialchars(root_path()) ?></code></p>
                            <p class="iw-small">Target: <code><?= htmlspecialchars(config_path()) ?></code></p>
                        </div>
                    </div>

                    <form method="post" class="iw-actions">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                        <input type="hidden" name="action" value="step1">
                        <button class="iw-btn" <?= (!$sample_exists || !$root_writable) ? 'disabled' : '' ?>>Start Setup</button>
                    </form>
                <?php endif; ?>

                <?php if($step === 2): ?>
                    <header class="iw-head">
                        <div class="iw-pill">Step 2 of 5</div>
                        <h2>Database connection</h2>
                        <p>Choose the environment, enter the database details, and test the connection before continuing.</p>
                    </header>

                    <form method="post" class="iw-form">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                        <input type="hidden" name="action" value="step2">

                        <div class="iw-field full">
                            <label>Server Type</label>
                            <div class="iw-choice-row">
                                <label class="iw-choice">
                                    <input type="radio" name="environment" value="local" <?= $environment === 'local' ? 'checked' : '' ?>>
                                    <span>Local server</span>
                                </label>
                                <label class="iw-choice">
                                    <input type="radio" name="environment" value="remote" <?= $environment === 'remote' ? 'checked' : '' ?>>
                                    <span>Online server</span>
                                </label>
                            </div>
                            <p class="iw-small">Detected environment: <strong><?= htmlspecialchars($detected_environment) ?></strong>. You can override it here.</p>
                        </div>

                        <div class="iw-field full">
                            <label for="root_url">Root URL</label>
                            <input id="root_url" name="root_url" value="<?= htmlspecialchars($default_root) ?>" placeholder="Leave empty to auto-detect, e.g. http://localhost/myfolder">
                        </div>

                        <div class="iw-field">
                            <label for="db_name">Database Name</label>
                            <input id="db_name" name="db_name" value="<?= htmlspecialchars(old('db_name', 'pluginphp_db')) ?>" required>
                        </div>
                        <div class="iw-field">
                            <label for="db_user">Database User</label>
                            <input id="db_user" name="db_user" value="<?= htmlspecialchars(old('db_user', $environment === 'local' ? 'root' : '')) ?>" required>
                        </div>
                        <div class="iw-field">
                            <label for="db_password">Database Password</label>
                            <input id="db_password" name="db_password" type="password" value="<?= htmlspecialchars(old('db_password', '')) ?>">
                        </div>
                        <div class="iw-field">
                            <label for="db_host">Database Host</label>
                            <input id="db_host" name="db_host" value="<?= htmlspecialchars(old('db_host', 'localhost')) ?>" required>
                        </div>
                        <div class="iw-field">
                            <label for="db_driver">Database Driver</label>
                            <input id="db_driver" name="db_driver" value="<?= htmlspecialchars($default_driver) ?>" placeholder="mysql" required>
                        </div>
                        <div class="iw-field">
                            <label for="db_port">Database Port</label>
                            <input id="db_port" name="db_port" value="<?= htmlspecialchars($default_port) ?>" placeholder="3306" required>
                        </div>

                        <div class="iw-actions full">
                            <a href="?step=1" class="iw-btn ghost">Back</a>
                            <button class="iw-btn">Test Connection & Continue</button>
                        </div>
                    </form>
                <?php endif; ?>

                <?php if($step === 3): ?>
                    <header class="iw-head">
                        <div class="iw-pill">Step 3 of 5</div>
                        <h2>Application details</h2>
                        <p>These values will be written to the app constants in <code>config.php</code>.</p>
                    </header>

                    <form method="post" enctype="multipart/form-data" class="iw-form">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                        <input type="hidden" name="action" value="step3">

                        <div class="iw-field full">
                            <label for="app_name">App Name</label>
                            <input id="app_name" name="app_name" value="<?= htmlspecialchars(old('app_name', 'Thunder PHP')) ?>" required>
                        </div>

                        <div class="iw-field full">
                            <label for="app_description">App Description</label>
                            <textarea id="app_description" name="app_description" rows="4" required><?= htmlspecialchars(old('app_description', 'A plugin based PHP Framework')) ?></textarea>
                        </div>

                        <div class="iw-field full">
                            <label>Logo</label>
                            <div class="iw-logo-box">
                                <?php if(!empty($logo_url)): ?>
                                    <img src="<?= htmlspecialchars($logo_url) ?>" alt="Current logo">
                                <?php else: ?>
                                    <div class="iw-logo-empty">No logo found</div>
                                <?php endif; ?>
                                <div>
                                    <input type="file" name="app_logo_file" accept="image/jpeg,image/png,image/gif,image/webp">
                                    <p class="iw-small">Optional. If uploaded, the image will be resized to a maximum of 512px and saved as <code>/assets/images/logo.jpg</code>.</p>
                                </div>
                            </div>
                        </div>

                        <label class="iw-toggle full">
                            <input type="checkbox" name="use_sessions" value="1" <?= old('use_sessions', true) ? 'checked' : '' ?>>
                            <span>Allow sessions for user login</span>
                        </label>

                        <label class="iw-toggle full">
                            <input type="checkbox" name="debug" value="1" <?= old('debug', true) ? 'checked' : '' ?>>
                            <span>Enable debug mode</span>
                        </label>

                        <div class="iw-actions full">
                            <a href="?step=2" class="iw-btn ghost">Back</a>
                            <button class="iw-btn">Create Config & Run Migrations</button>
                        </div>
                    </form>
                <?php endif; ?>

                <?php if($step === 4): ?>
                    <header class="iw-head">
                        <div class="iw-pill">Step 4 of 5</div>
                        <h2>Run plugin migrations</h2>
                        <p>The config file has now been saved. This page was loaded again so the new database constants are available before running all plugin migrations.</p>
                    </header>

                    <div class="iw-card">
                        <h3>Migration Result</h3>

                        <?php if(($migration_result['ok'] ?? null) === true): ?>
                            <div class="iw-check ok"><span></span> Migrations completed successfully.</div>
                        <?php elseif(($migration_result['ok'] ?? null) === false): ?>
                            <div class="iw-check bad"><span></span> Migrations failed.</div>
                        <?php else: ?>
                            <div class="iw-check"><span></span> Migrations have not run yet.</div>
                        <?php endif; ?>

                        <?php if(!empty($migration_result['output'])): ?>
                            <pre class="iw-output"><?= htmlspecialchars((string) $migration_result['output']) ?></pre>
                        <?php else: ?>
                            <p class="iw-small">If this screen does not show migration output, click “Run Again”.</p>
                        <?php endif; ?>
                    </div>

                    <form method="post" class="iw-form single">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                        <div class="iw-actions full">
                            <button class="iw-btn ghost" name="action" value="rerun_migrations" type="submit">Run Again</button>
                            <button class="iw-btn ghost" name="action" value="skip_migrations" type="submit">Skip Migrations</button>
                            <a href="?step=5" class="iw-btn">Continue</a>
                        </div>
                    </form>
                <?php endif; ?>

                <?php if($step === 5): ?>
                    <header class="iw-head iw-complete">
                        <div class="iw-success-mark">✓</div>
                        <div class="iw-pill">Step 5 of 5</div>
                        <h2>Congratulations. ThunderPHP is ready.</h2>
                        <p>Your <code>config.php</code> file has been created and the installation flow is complete. You can now continue to the login page.</p>
                    </header>

                    <form method="post" class="iw-form single">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                        <input type="hidden" name="action" value="finish">

                        <label class="iw-toggle full strong">
                            <input type="checkbox" name="disable_wizard" value="1" checked>
                            <span>Disable this wizard plugin after installation</span>
                        </label>

                        <p class="iw-small">Recommended. This prevents the installer from being available after setup. You can enable it manually again by editing the plugin <code>config.json</code> file if needed.</p>

                        <div class="iw-actions full">
                            <button class="iw-btn">Go to Login</button>
                        </div>
                    </form>
                <?php endif; ?>
            </section>
        </section>
    </main>
</body>
</html>
