<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$values = get_value('thunder_config_values') ?? [];
$configPath = get_value('thunder_config_path') ?? '';
$writable = get_value('thunder_config_writable') ?? false;

function tc_checked($value): string
{
    return !empty($value) ? 'checked' : '';
}

function tc_value(array $values, string $key, $default = '')
{
    return $values[$key] ?? $default;
}
?>

<link href="<?=plugin_http_path('looks/main/assets/css/config.css')?>" rel="stylesheet">

<div class="tc-wrap">
    <div class="tc-hero">
        <div>
            <div class="tc-kicker">ThunderPHP</div>
            <h2>Application Configuration</h2>
            <p>Edit safe values from your root <code>config.php</code> file without opening your code editor.</p>
        </div>
        <div class="tc-path-card <?= $writable ? 'tc-ok' : 'tc-bad' ?>">
            <span><?= $writable ? 'Writable' : 'Not Writable' ?></span>
            <small><?=thunder_config_e($configPath)?></small>
        </div>
    </div>

    <div class="tc-grid">
        <form method="post" class="tc-card tc-form">
            <?=csrf()?>

            <div class="tc-section-head">
                <h3>Website Identity</h3>
                <p>These values control the visible application name, description, and logo path.</p>
            </div>

            <div class="tc-field">
                <label>Application Name</label>
                <input type="text" name="APP_NAME" value="<?=thunder_config_e(tc_value($values, 'APP_NAME'))?>">
            </div>

            <div class="tc-field">
                <label>Application Description</label>
                <textarea name="APP_DESCRIPTION" rows="3"><?=thunder_config_e(tc_value($values, 'APP_DESCRIPTION'))?></textarea>
            </div>

            <div class="tc-logo-box">
                <div class="tc-logo-preview">
                    <?php if(!empty(tc_value($values, 'APP_LOGO'))): ?>
                        <img id="tcLogoPreview" src="<?=thunder_config_e(base_url(ltrim(tc_value($values, 'APP_LOGO'), '/')))?>" alt="Application Logo">
                    <?php else: ?>
                        <div id="tcLogoPreview" class="tc-empty-logo"><i class="fa-solid fa-image"></i></div>
                    <?php endif; ?>
                </div>
                <div class="tc-logo-controls">
                    <label>Logo Path</label>
                    <input type="text" id="tcLogoPath" name="APP_LOGO" value="<?=thunder_config_e(tc_value($values, 'APP_LOGO'))?>">

                    <label class="tc-upload-label" for="tcLogoInput" style="color:white">
                        <i class="fa-solid fa-upload"></i>
                        Upload Logo
                    </label>
                    <input id="tcLogoInput" type="file" accept="image/jpeg,image/png,image/gif,image/webp" data-upload-url="<?=base_url('admin/thunder-config/upload-logo')?>">
                    <div class="tc-progress" id="tcUploadProgressWrap"><span id="tcUploadProgress"></span></div>
                    <div class="tc-upload-status" id="tcUploadStatus"></div>
                    <p class="tc-note">Uploaded logos are resized proportionally so the largest side is no more than 512px.</p>
                </div>
            </div>

            <div class="tc-section-head">
                <h3>Runtime Options</h3>
                <p>Boolean options are saved as PHP <code>true</code> or <code>false</code>.</p>
            </div>

            <div class="tc-switch-row">
                <label class="tc-switch">
                    <input type="checkbox" name="USE_SESSIONS" value="1" <?=tc_checked(tc_value($values, 'USE_SESSIONS'))?>>
                    <span></span>
                </label>
                <div>
                    <strong>Use Sessions</strong>
                    <small>Allow session support for login and user state.</small>
                </div>
            </div>

            <div class="tc-switch-row">
                <label class="tc-switch">
                    <input type="checkbox" name="DEBUG" value="1" <?=tc_checked(tc_value($values, 'DEBUG'))?>>
                    <span></span>
                </label>
                <div>
                    <strong>Debug Mode</strong>
                    <small>Enable or disable error reporting.</small>
                </div>
            </div>

            <div class="tc-section-head">
                <h3>Root URLs</h3>
                <p>Leave empty to allow automatic detection.</p>
            </div>

            <div class="tc-two">
                <div class="tc-field">
                    <label>Local Root</label>
                    <input type="text" name="LOCAL_ROOT" value="<?=thunder_config_e(tc_value($values, 'LOCAL_ROOT'))?>" placeholder="http://localhost/myfolder">
                </div>
                <div class="tc-field">
                    <label>Remote Root</label>
                    <input type="text" name="REMOTE_ROOT" value="<?=thunder_config_e(tc_value($values, 'REMOTE_ROOT'))?>" placeholder="https://example.com">
                </div>
            </div>

            <div class="tc-section-head">
                <h3>Local Database</h3>
                <p>Values used in your local development environment.</p>
            </div>

            <div class="tc-two">
                <div class="tc-field"><label>Database Name</label><input type="text" name="LOCAL_DB_NAME" value="<?=thunder_config_e(tc_value($values, 'LOCAL_DB_NAME'))?>"></div>
                <div class="tc-field"><label>Database User</label><input type="text" name="LOCAL_DB_USER" value="<?=thunder_config_e(tc_value($values, 'LOCAL_DB_USER'))?>"></div>
                <div class="tc-field"><label>Database Password</label><input type="password" name="LOCAL_DB_PASSWORD" value="<?=thunder_config_e(tc_value($values, 'LOCAL_DB_PASSWORD'))?>" autocomplete="new-password"></div>
                <div class="tc-field"><label>Database Host</label><input type="text" name="LOCAL_DB_HOST" value="<?=thunder_config_e(tc_value($values, 'LOCAL_DB_HOST'))?>"></div>
                <div class="tc-field"><label>Database Driver</label><input type="text" name="LOCAL_DB_DRIVER" value="<?=thunder_config_e(tc_value($values, 'LOCAL_DB_DRIVER'))?>"></div>
                <div class="tc-field"><label>Database Port</label><input type="text" name="LOCAL_DB_PORT" value="<?=thunder_config_e(tc_value($values, 'LOCAL_DB_PORT'))?>"></div>
            </div>

            <div class="tc-section-head">
                <h3>Remote Database</h3>
                <p>Values used in your live or remote environment.</p>
            </div>

            <div class="tc-two">
                <div class="tc-field"><label>Database Name</label><input type="text" name="REMOTE_DB_NAME" value="<?=thunder_config_e(tc_value($values, 'REMOTE_DB_NAME'))?>"></div>
                <div class="tc-field"><label>Database User</label><input type="text" name="REMOTE_DB_USER" value="<?=thunder_config_e(tc_value($values, 'REMOTE_DB_USER'))?>"></div>
                <div class="tc-field"><label>Database Password</label><input type="password" name="REMOTE_DB_PASSWORD" value="<?=thunder_config_e(tc_value($values, 'REMOTE_DB_PASSWORD'))?>" autocomplete="new-password"></div>
                <div class="tc-field"><label>Database Host</label><input type="text" name="REMOTE_DB_HOST" value="<?=thunder_config_e(tc_value($values, 'REMOTE_DB_HOST'))?>"></div>
                <div class="tc-field"><label>Database Driver</label><input type="text" name="REMOTE_DB_DRIVER" value="<?=thunder_config_e(tc_value($values, 'REMOTE_DB_DRIVER'))?>"></div>
                <div class="tc-field"><label>Database Port</label><input type="text" name="REMOTE_DB_PORT" value="<?=thunder_config_e(tc_value($values, 'REMOTE_DB_PORT'))?>"></div>
            </div>
            <button type="button" class="tc-btn tc-btn-secondary" id="tc-test-db-btn">
                Test Database Connection
            </button>

            <small><div class="tc-test-result" id="tc-test-db-result"></div></small>

            <div class="tc-actions">
                <button class="tc-btn tc-btn-primary" type="submit" <?=!$writable ? 'disabled' : ''?>>
                    <i class="fa-solid fa-floppy-disk"></i> Save Configuration
                </button>
                <a class="tc-btn" href="<?=base_url('admin')?>">
                    <i class="fa-solid fa-arrow-left"></i> Back to Admin
                </a>
            </div>
        </form>

        <aside class="tc-card tc-help">
            <h3>Safety Notes</h3>
            <p>This plugin only edits a fixed allow-list of <code>define()</code> values. It creates a timestamped backup before writing changes.</p>
            <ul>
                <li>Do not expose this page to non-admin users.</li>
                <li>Keep database passwords protected.</li>
                <li>After changing constants, reload the page to see runtime changes.</li>
                <li>If saving fails, check file permissions on <code>config.php</code>.</li>
            </ul>
        </aside>
    </div>
</div>

<script>
window.THUNDER_CONFIG_CSRF = <?=json_encode(function_exists('csrf') ? csrf() : '')?>;
</script>
<script src="<?=plugin_http_path('looks/main/assets/js/config.js')?>"></script>
<script>

document.addEventListener('DOMContentLoaded', function(){

    const btn = document.querySelector('#tc-test-db-btn');
    const result = document.querySelector('#tc-test-db-result');

    if(!btn || !result) return;

    btn.addEventListener('click', function(){

        result.textContent = 'Testing connection...';
        result.className = 'tc-test-result tc-is-loading';

        const form = btn.closest('form');
        const data = new FormData(form);

        data.append('action', 'test_database_config');

        fetch(window.location.href, {
            method: 'POST',
            body: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(json => {
            let text = '<br>';
            text += 'local: '+(json.local || 'No response received.');
            text += "<br>remote: "+(json.remote || 'No response received.');
            result.innerHTML = text;
            result.className = json.success
                ? 'tc-test-result tc-is-success'
                : 'tc-test-result tc-is-error';
        })
        .catch(() => {
            result.textContent = 'Connection test failed unexpectedly.';
            result.className = 'tc-test-result tc-is-error';
        });

    });

});
</script>

