<?php
/**
 * ThunderPHP verbose error page.
 *
 * All diagnostic variables are prepared by thunder_debug_render() in init.php.
 * This template remains defensive so it can still render if part of the
 * framework bootstrap is incomplete.
 */

$error_esc = static function ($value) {
    if ($value === null) {
        $value = 'null';
    } elseif (is_bool($value)) {
        $value = $value ? 'true' : 'false';
    } elseif (!is_scalar($value)) {
        $value = '[complex value]';
    }

    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$json_flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
    $json_flags |= JSON_INVALID_UTF8_SUBSTITUTE;
}

$error_json = static function ($value) use ($json_flags) {
    $json = json_encode($value, $json_flags);

    if ($json === false) {
        return 'Unable to encode diagnostic data: ' . json_last_error_msg();
    }

    return $json;
};

$error_display = static function ($value) use ($error_json) {
    if (is_array($value) || is_object($value)) {
        return $error_json($value);
    }

    if ($value === null) {
        return 'null';
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    return (string) $value;
};

$app_name = defined('APP_NAME') ? APP_NAME : 'ThunderPHP';
$app_environment = defined('APP_ENV')
    ? APP_ENV
    : (defined('DEBUG') && DEBUG ? 'Debug' : 'Production');

$error_type = isset($error_type) && is_array($error_type)
    ? $error_type
    : ['title' => 'ERROR', 'type' => 'Application Error', 'severity' => 'Unknown'];
$errno = isset($errno) ? $errno : E_ERROR;
$errstr = isset($errstr) ? $errstr : 'Unknown application error';
$errfile = isset($errfile) ? $errfile : 'Unknown file';
$errline = isset($errline) ? $errline : 0;
$error_code = isset($error_code) ? $error_code : $errno;
$error_timestamp = isset($error_timestamp) ? $error_timestamp : time();
$request_id = isset($request_id) ? $request_id : 'Not available';
$http_status = isset($http_status) ? $http_status : 500;
$request_details = isset($request_details) && is_array($request_details) ? $request_details : [];
$input_data = isset($input_data) ? $input_data : [];
$session_data = isset($session_data) ? $session_data : [];
$env_data = isset($env_data) ? $env_data : [];
$extra_context = isset($extra_context) ? $extra_context : [];
$application_state = isset($application_state) && is_array($application_state) ? $application_state : [];
$matched_routes = isset($matched_routes) && is_array($matched_routes) ? $matched_routes : [];
$trace = isset($trace) && is_array($trace) ? $trace : [];
$fatal_trace_lines = isset($fatal_trace_lines) && is_array($fatal_trace_lines) ? $fatal_trace_lines : [];
$source_excerpt = isset($source_excerpt) && is_array($source_excerpt) ? $source_excerpt : [];

$current_route = isset($application_state['current_route']) ? $application_state['current_route'] : 'Not available';
$controller = isset($application_state['controller']) ? $application_state['controller'] : 'Not available';
$action = isset($application_state['action']) ? $application_state['action'] : 'Not available';
$user_info = isset($application_state['user_info']) ? $application_state['user_info'] : 'Not available';
$app_state_snapshot = isset($application_state['app_state']) ? $application_state['app_state'] : [];

$hook_runtime = isset($hook_runtime) && is_array($hook_runtime)
    ? $hook_runtime
    : (isset($app_state_snapshot['hook_runtime']) && is_array($app_state_snapshot['hook_runtime'])
        ? $app_state_snapshot['hook_runtime']
        : []);
$hook_last_error = isset($hook_runtime['last_error']) && is_array($hook_runtime['last_error'])
    ? $hook_runtime['last_error']
    : [];
$hook_active_stack = isset($hook_last_error['active_stack']) && is_array($hook_last_error['active_stack'])
    ? $hook_last_error['active_stack']
    : (isset($hook_runtime['active_stack']) && is_array($hook_runtime['active_stack'])
        ? $hook_runtime['active_stack']
        : []);
$hook_execution_path = isset($hook_last_error['path']) && is_array($hook_last_error['path'])
    ? $hook_last_error['path']
    : $hook_active_stack;
$hook_history = isset($hook_last_error['execution_history']) && is_array($hook_last_error['execution_history'])
    ? $hook_last_error['execution_history']
    : (isset($hook_runtime['execution_history']) && is_array($hook_runtime['execution_history'])
        ? $hook_runtime['execution_history']
        : []);
$hook_history_dropped = isset($hook_last_error['history_dropped'])
    ? (int) $hook_last_error['history_dropped']
    : (isset($hook_runtime['history_dropped']) ? (int) $hook_runtime['history_dropped'] : 0);
$hook_error_kind = isset($hook_last_error['kind']) ? (string) $hook_last_error['kind'] : '';

$hook_actor = static function (array $frame) {
    if (!empty($frame['current_callback']) && is_array($frame['current_callback'])) {
        return $frame['current_callback'];
    }

    if (!empty($frame['requested_by']) && is_array($frame['requested_by'])) {
        return $frame['requested_by'];
    }

    return [];
};

$hook_plugin_label = static function (array $actor) {
    $id = isset($actor['plugin_id']) ? trim((string) $actor['plugin_id']) : '';
    $name = isset($actor['plugin_name']) ? trim((string) $actor['plugin_name']) : '';

    if ($name !== '' && $id !== '' && $name !== $id) {
        return $name . ' (' . $id . ')';
    }

    return $name !== '' ? $name : ($id !== '' ? $id : 'Core / direct call');
};

$hook_route_label = static function (array $actor) {
    $registered = isset($actor['registered_route']) ? trim((string) $actor['registered_route']) : '';
    if ($registered !== '') {
        return $registered;
    }

    $matched = isset($actor['matched_routes']) && is_array($actor['matched_routes'])
        ? array_filter(array_map('strval', $actor['matched_routes']))
        : [];

    return !empty($matched) ? 'All routes; matched: ' . implode(', ', $matched) : 'All routes';
};

$hook_source_label = static function (array $actor) {
    $file = isset($actor['file']) ? trim((string) $actor['file']) : '';
    $line = isset($actor['line']) && $actor['line'] !== null ? (int) $actor['line'] : 0;

    if ($file === '') {
        return 'Not available';
    }

    return $file . ($line > 0 ? ':' . $line : '');
};

$hook_call_site_label = static function (array $frame) {
    $site = isset($frame['call_site']) && is_array($frame['call_site'])
        ? $frame['call_site']
        : [];
    $file = isset($site['relative_file']) && trim((string) $site['relative_file']) !== ''
        ? trim((string) $site['relative_file'])
        : (isset($site['file']) ? trim((string) $site['file']) : '');
    $line = isset($site['line']) ? (int) $site['line'] : 0;

    if ($file === '') {
        return 'Not available';
    }

    return $file . ($line > 0 ? ':' . $line : '');
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex,nofollow" />
  <title><?= $error_esc($error_type['type']) ?> | <?= $error_esc($app_name) ?></title>
  <style>
    :root{
      --bg:#0f172a;
      --panel:#111827;
      --panel-2:#1f2937;
      --border:#334155;
      --text:#e5e7eb;
      --muted:#94a3b8;
      --danger:#ef4444;
      --warning:#f59e0b;
      --info:#38bdf8;
      --success:#22c55e;
      --code:#0b1220;
      --shadow:0 10px 30px rgba(0,0,0,.25);
      --radius:16px;
    }

    *{box-sizing:border-box;}

    body{
      margin:0;
      font-family:Arial, Helvetica, sans-serif;
      background:
        radial-gradient(circle at top right, rgba(239,68,68,.12), transparent 30%),
        radial-gradient(circle at bottom left, rgba(56,189,248,.10), transparent 30%),
        var(--bg);
      color:var(--text);
      line-height:1.5;
    }

    .wrap{
      width:min(1200px, calc(100% - 32px));
      margin:30px auto;
    }

    .hero{
      background:linear-gradient(180deg, rgba(239,68,68,.12), rgba(255,255,255,.02));
      border:1px solid var(--border);
      border-radius:var(--radius);
      padding:24px;
      box-shadow:var(--shadow);
      margin-bottom:20px;
    }

    .hero-top{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
      flex-wrap:wrap;
      margin-bottom:12px;
    }

    .title{
      display:flex;
      align-items:center;
      gap:14px;
    }

    .icon{
      width:52px;
      height:52px;
      min-width:52px;
      border-radius:14px;
      background:rgba(239,68,68,.15);
      border:1px solid rgba(239,68,68,.35);
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:24px;
    }

    h1{margin:0;font-size:28px;}

    .subtitle{
      margin:4px 0 0;
      color:var(--muted);
      font-size:14px;
    }

    .badges,.toolbar{
      display:flex;
      gap:8px;
      flex-wrap:wrap;
    }

    .badge{
      padding:8px 12px;
      border-radius:999px;
      font-size:12px;
      font-weight:bold;
      border:1px solid var(--border);
      background:rgba(255,255,255,.04);
    }

    .badge-danger{
      background:rgba(239,68,68,.14);
      border-color:rgba(239,68,68,.35);
      color:#fecaca;
    }

    .badge-warning{
      background:rgba(245,158,11,.14);
      border-color:rgba(245,158,11,.35);
      color:#fde68a;
    }

    .message{
      margin-top:14px;
      padding:16px 18px;
      border-left:4px solid var(--danger);
      background:rgba(255,255,255,.03);
      border-radius:10px;
      font-size:15px;
      overflow-wrap:anywhere;
    }

    .message-path{
      margin-top:10px;
      color:var(--muted);
      font-family:Consolas, Monaco, monospace;
      font-size:13px;
    }

    .grid{
      display:grid;
      grid-template-columns:repeat(12,1fr);
      gap:20px;
    }

    .card{
      grid-column:span 12;
      background:var(--panel);
      border:1px solid var(--border);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
      overflow:hidden;
    }

    .card.half{grid-column:span 6;}
    .card.third{grid-column:span 4;}

    .card-header{
      padding:16px 18px;
      background:rgba(255,255,255,.03);
      border-bottom:1px solid var(--border);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
    }

    .card-header h2{margin:0;font-size:16px;}
    .card-body{padding:18px;}

    table{
      width:100%;
      border-collapse:collapse;
    }

    th,td{
      text-align:left;
      padding:12px 10px;
      border-bottom:1px solid rgba(148,163,184,.16);
      vertical-align:top;
      font-size:14px;
    }

    th{
      width:220px;
      color:var(--muted);
      font-weight:600;
      background:rgba(255,255,255,.02);
    }

    td{word-break:break-word;}

    .codebox{
      margin:0;
      background:var(--code);
      border:1px solid var(--border);
      border-radius:12px;
      padding:14px;
      overflow:auto;
      max-height:520px;
      font-family:Consolas, Monaco, monospace;
      font-size:13px;
      line-height:1.55;
      white-space:pre-wrap;
      word-break:break-word;
    }

    .stack-line{
      padding:14px;
      border:1px solid rgba(148,163,184,.14);
      background:rgba(255,255,255,.015);
      border-radius:12px;
      margin-bottom:12px;
      font-family:Consolas, Monaco, monospace;
      font-size:13px;
    }

    .stack-line:last-child{margin-bottom:0;}

    .stack-heading{
      display:flex;
      gap:10px;
      align-items:flex-start;
      margin-bottom:8px;
    }

    .stack-number{
      color:var(--info);
      font-weight:700;
      min-width:34px;
    }

    .stack-call{font-weight:700;overflow-wrap:anywhere;}
    .stack-location{color:var(--muted);overflow-wrap:anywhere;}
    .stack-args{margin-top:10px;}

    .hook-section-title{
      margin:0 0 12px;
      font-size:14px;
      color:var(--muted);
      text-transform:uppercase;
      letter-spacing:.06em;
    }

    .hook-section-title:not(:first-child){margin-top:24px;}
    .hook-table th{width:auto;white-space:nowrap;}
    .hook-table td{font-family:Consolas, Monaco, monospace;font-size:13px;}
    .hook-attempted td{background:rgba(245,158,11,.08);}
    .hook-cycle-start td{background:rgba(239,68,68,.06);}
    .hook-status{
      display:inline-block;
      padding:4px 8px;
      border:1px solid var(--border);
      border-radius:999px;
      font-family:Arial, Helvetica, sans-serif;
      font-size:11px;
      white-space:nowrap;
    }
    .hook-status-warning{border-color:rgba(245,158,11,.45);color:#fde68a;background:rgba(245,158,11,.10);}
    .hook-status-danger{border-color:rgba(239,68,68,.45);color:#fecaca;background:rgba(239,68,68,.10);}

    .kv{
      display:grid;
      grid-template-columns:180px 1fr;
      gap:10px;
      padding:10px 0;
      border-bottom:1px solid rgba(148,163,184,.12);
    }

    .kv:last-child{border-bottom:none;}
    .kv .k{color:var(--muted);font-weight:600;}
    .kv .v{overflow-wrap:anywhere;}

    .source-code{
      background:var(--code);
      border:1px solid var(--border);
      border-radius:12px;
      overflow:auto;
      font-family:Consolas, Monaco, monospace;
      font-size:13px;
    }

    .source-line{
      display:grid;
      grid-template-columns:72px minmax(0,1fr);
      min-width:max-content;
    }

    .source-line.error-line{
      background:rgba(239,68,68,.15);
      border-left:4px solid var(--danger);
    }

    .line-number{
      padding:4px 12px;
      text-align:right;
      color:var(--muted);
      user-select:none;
      border-right:1px solid rgba(148,163,184,.15);
    }

    .line-code{
      padding:4px 14px;
      white-space:pre;
    }

    .empty{
      color:var(--muted);
      padding:12px 0;
      font-style:italic;
    }

    .footer{
      margin-top:20px;
      color:var(--muted);
      font-size:13px;
      text-align:center;
    }

    button{
      background:var(--panel-2);
      color:var(--text);
      border:1px solid var(--border);
      border-radius:10px;
      padding:10px 14px;
      cursor:pointer;
      font-weight:600;
    }

    button:hover{background:#263244;}
    .hidden{display:none;}

    @media (max-width:900px){
      .card.half,.card.third{grid-column:span 12;}
      th{width:140px;}
      .kv{grid-template-columns:1fr;gap:2px;}
    }
  </style>
</head>
<body>
  <div class="wrap" id="errorReport">

    <section class="hero">
      <div class="hero-top">
        <div class="title">
          <div class="icon">⚠</div>
          <div>
            <h1><?= $error_esc($error_type['type']) ?></h1>
            <p class="subtitle">An unexpected error occurred while processing the request.</p>
          </div>
        </div>

        <div class="badges">
          <span class="badge badge-danger"><?= $error_esc($error_type['severity']) ?></span>
          <span class="badge badge-warning">HTTP <?= $error_esc($http_status) ?></span>
          <span class="badge"><?= $error_esc(PHP_VERSION) ?></span>
          <span class="badge"><?= $error_esc(PHP_OS) ?></span>
        </div>
      </div>

      <div class="message">
        <strong><?= $error_esc($error_type['title']) ?>:</strong>
        <?= $error_esc($errstr) ?>
        <div class="message-path">
          <?= $error_esc($errfile) ?> on line <?= $error_esc($errline) ?>
        </div>
      </div>
    </section>

    <div class="grid">

      <section class="card half">
        <div class="card-header"><h2>Error Summary</h2></div>
        <div class="card-body">
          <div class="kv"><div class="k">Timestamp</div><div class="v"><?= $error_esc(date('Y-m-d H:i:s T', $error_timestamp)) ?></div></div>
          <div class="kv"><div class="k">Error Number</div><div class="v"><?= $error_esc($errno) ?></div></div>
          <div class="kv"><div class="k">Error / Exception Code</div><div class="v"><?= $error_esc($error_code) ?></div></div>
          <div class="kv"><div class="k">Severity</div><div class="v"><?= $error_esc($error_type['severity']) ?></div></div>
          <div class="kv"><div class="k">File</div><div class="v"><?= $error_esc($errfile) ?></div></div>
          <div class="kv"><div class="k">Line</div><div class="v"><?= $error_esc($errline) ?></div></div>
          <div class="kv"><div class="k">Request ID</div><div class="v"><?= $error_esc($request_id) ?></div></div>
        </div>
      </section>

      <section class="card half">
        <div class="card-header"><h2>Application State</h2></div>
        <div class="card-body">
          <div class="kv"><div class="k">App Name</div><div class="v"><?= $error_esc($app_name) ?></div></div>
          <div class="kv"><div class="k">Environment</div><div class="v"><?= $error_esc($app_environment) ?></div></div>
          <div class="kv"><div class="k">PHP Version</div><div class="v"><?= $error_esc(PHP_VERSION) ?></div></div>
          <div class="kv"><div class="k">Current Route</div><div class="v"><?= $error_esc($error_display($current_route)) ?></div></div>
          <div class="kv"><div class="k">Controller</div><div class="v"><?= $error_esc($error_display($controller)) ?></div></div>
          <div class="kv"><div class="k">Action</div><div class="v"><?= $error_esc($error_display($action)) ?></div></div>
          <div class="kv"><div class="k">Logged In User</div><div class="v"><pre class="codebox"><?= $error_esc($error_display($user_info)) ?></pre></div></div>
        </div>
      </section>

      <?php if (!empty($hook_execution_path) || !empty($hook_active_stack)): ?>
        <?php
          $last_path_frame = !empty($hook_execution_path) ? $hook_execution_path[array_key_last($hook_execution_path)] : [];
          $repeated_from = isset($last_path_frame['repeated_from']) && $last_path_frame['repeated_from'] !== null
              ? (int) $last_path_frame['repeated_from']
              : null;
        ?>
        <section class="card">
          <div class="card-header">
            <h2>Hook Diagnostics</h2>
            <?php if ($hook_error_kind !== ''): ?>
              <span class="badge badge-warning"><?= $error_esc(str_replace('_', ' ', strtoupper($hook_error_kind))) ?></span>
            <?php endif; ?>
          </div>
          <div class="card-body">
            <?php if (!empty($hook_execution_path)): ?>
              <h3 class="hook-section-title">Complete Hook Path</h3>
              <div style="overflow:auto">
                <table class="hook-table">
                  <thead>
					<tr><th>#</th><th>Hook</th><th>Plugin / Callback</th><th>Priority</th><th>Route</th><th>Callback Source</th><th>Hook Call</th><th>Status</th></tr>
                  </thead>
                  <tbody>
                    <?php foreach ($hook_execution_path as $index => $frame): ?>
                      <?php
                        $actor = $hook_actor(is_array($frame) ? $frame : []);
                        $is_attempted = !empty($frame['attempted']);
                        $is_cycle_start = $repeated_from !== null && $repeated_from === $index;
                        $status = $is_attempted
                            ? ($hook_error_kind === 'depth' ? 'Depth limit attempt' : 'Re-entry attempt')
                            : ($is_cycle_start ? 'Cycle start' : 'Active');
                        $row_class = $is_attempted ? 'hook-attempted' : ($is_cycle_start ? 'hook-cycle-start' : '');
                      ?>
                      <tr class="<?= $error_esc($row_class) ?>">
                        <td><?= $error_esc($index + 1) ?></td>
                        <td><strong><?= $error_esc(strtoupper((string) ($frame['type'] ?? 'hook'))) ?></strong>:<?= $error_esc((string) ($frame['hook'] ?? 'unknown')) ?></td>
                        <td>
                          <?= $error_esc($hook_plugin_label($actor)) ?><br>
                          <span class="stack-location"><?= $error_esc(isset($actor['callback']) ? $actor['callback'] : 'No callback entered') ?></span>
                        </td>
                        <td><?= $error_esc(isset($actor['priority']) ? $actor['priority'] : '—') ?></td>
                        <td><?= $error_esc($hook_route_label($actor)) ?></td>
                        <td><?= $error_esc($hook_source_label($actor)) ?></td>
						<td><?= $error_esc($hook_call_site_label(is_array($frame) ? $frame : [])) ?></td>
                        <td><span class="hook-status<?= $is_attempted ? ' hook-status-warning' : ($is_cycle_start ? ' hook-status-danger' : '') ?>"><?= $error_esc($status) ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>

            <?php if (!empty($hook_active_stack)): ?>
              <h3 class="hook-section-title">Active Hook Stack at Error</h3>
              <div style="overflow:auto">
                <table class="hook-table">
                  <thead>
					<tr><th>Depth</th><th>Hook</th><th>Plugin / Callback</th><th>Route</th><th>Callback Source</th><th>Hook Call</th></tr>
                  </thead>
                  <tbody>
                    <?php foreach ($hook_active_stack as $index => $frame): ?>
                      <?php $actor = $hook_actor(is_array($frame) ? $frame : []); ?>
                      <tr>
                        <td><?= $error_esc(isset($frame['depth']) ? $frame['depth'] : $index + 1) ?></td>
                        <td><strong><?= $error_esc(strtoupper((string) ($frame['type'] ?? 'hook'))) ?></strong>:<?= $error_esc((string) ($frame['hook'] ?? 'unknown')) ?></td>
                        <td>
                          <?= $error_esc($hook_plugin_label($actor)) ?><br>
                          <span class="stack-location"><?= $error_esc(isset($actor['callback']) ? $actor['callback'] : 'No callback entered') ?></span>
                        </td>
                        <td><?= $error_esc($hook_route_label($actor)) ?></td>
                        <td><?= $error_esc($hook_source_label($actor)) ?></td>
						<td><?= $error_esc($hook_call_site_label(is_array($frame) ? $frame : [])) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </section>
      <?php endif; ?>

      <?php if (!empty($hook_history)): ?>
        <section class="card">
          <div class="card-header">
            <h2>Hook Callback History</h2>
            <div class="toolbar">
              <span class="badge"><?= $error_esc(count($hook_history)) ?> recorded</span>
              <button type="button" onclick="toggleSection('hookHistory')">Show / Hide</button>
            </div>
          </div>
          <div class="card-body hidden" id="hookHistory">
            <?php if ($hook_history_dropped > 0): ?>
              <div class="message" style="border-left-color:var(--warning);margin:0 0 14px">
                <?= $error_esc($hook_history_dropped) ?> older callback entries were omitted by the configured history limit.
              </div>
            <?php endif; ?>
            <div style="overflow:auto">
              <table class="hook-table">
                <thead>
                  <tr><th>#</th><th>Hook</th><th>Plugin / Callback</th><th>Priority</th><th>Route</th><th>Source</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($hook_history as $entry): ?>
                    <?php $entry = is_array($entry) ? $entry : []; ?>
                    <tr>
                      <td><?= $error_esc(isset($entry['sequence']) ? $entry['sequence'] : '—') ?></td>
                      <td><strong><?= $error_esc(strtoupper((string) ($entry['type'] ?? 'hook'))) ?></strong>:<?= $error_esc((string) ($entry['hook'] ?? 'unknown')) ?></td>
                      <td>
                        <?= $error_esc($hook_plugin_label($entry)) ?><br>
                        <span class="stack-location"><?= $error_esc(isset($entry['callback']) ? $entry['callback'] : 'Unknown callback') ?></span>
                      </td>
                      <td><?= $error_esc(isset($entry['priority']) ? $entry['priority'] : '—') ?></td>
                      <td><?= $error_esc($hook_route_label($entry)) ?></td>
                      <td><?= $error_esc($hook_source_label($entry)) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </section>
      <?php endif; ?>

      <section class="card">
        <div class="card-header"><h2>Request Details</h2></div>
        <div class="card-body">
          <table>
            <tr><th>Method</th><td><?= $error_esc(isset($request_details['method']) ? $request_details['method'] : 'Not available') ?></td></tr>
            <tr><th>URL</th><td><?= $error_esc(isset($request_details['url']) ? $request_details['url'] : 'Not available') ?></td></tr>
            <tr><th>IP Address</th><td><?= $error_esc(isset($request_details['ip_address']) ? $request_details['ip_address'] : 'Not available') ?></td></tr>
            <tr><th>Forwarded For</th><td><?= $error_esc(isset($request_details['forwarded_for']) ? $request_details['forwarded_for'] : 'Not available') ?></td></tr>
            <tr><th>User Agent</th><td><?= $error_esc(isset($request_details['user_agent']) ? $request_details['user_agent'] : 'Not available') ?></td></tr>
            <tr><th>Referrer</th><td><?= $error_esc(isset($request_details['referrer']) ? $request_details['referrer'] : 'Not available') ?></td></tr>
            <tr><th>Query String</th><td><?= $error_esc(isset($request_details['query_string']) ? $request_details['query_string'] : 'None') ?></td></tr>
          </table>
        </div>
      </section>

      <section class="card">
        <div class="card-header">
          <h2>Request Headers</h2>
          <div class="toolbar"><button type="button" onclick="toggleSection('requestHeaders')">Show / Hide</button></div>
        </div>
        <div class="card-body" id="requestHeaders">
          <pre class="codebox"><?= $error_esc($error_json(isset($request_details['headers']) ? $request_details['headers'] : [])) ?></pre>
        </div>
      </section>

      <?php if (!empty($source_excerpt)): ?>
        <section class="card">
          <div class="card-header"><h2>Source Context</h2></div>
          <div class="card-body">
            <div class="source-code">
              <?php foreach ($source_excerpt as $source_line): ?>
                <div class="source-line<?= !empty($source_line['is_error']) ? ' error-line' : '' ?>">
                  <span class="line-number"><?= $error_esc($source_line['number']) ?></span>
                  <span class="line-code"><?= $error_esc($source_line['code']) ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>
      <?php endif; ?>

      <section class="card half">
        <div class="card-header">
          <h2>Input Data</h2>
          <div class="toolbar"><button type="button" onclick="toggleSection('inputData')">Show / Hide</button></div>
        </div>
        <div class="card-body" id="inputData"><pre class="codebox"><?= $error_esc($error_json($input_data)) ?></pre></div>
      </section>

      <section class="card half">
        <div class="card-header">
          <h2>Session / Auth State</h2>
          <div class="toolbar"><button type="button" onclick="toggleSection('sessionData')">Show / Hide</button></div>
        </div>
        <div class="card-body" id="sessionData"><pre class="codebox"><?= $error_esc($error_json($session_data)) ?></pre></div>
      </section>

      <section class="card half">
        <div class="card-header">
          <h2>Environment Variables</h2>
          <div class="toolbar"><button type="button" onclick="toggleSection('environmentData')">Show / Hide</button></div>
        </div>
        <div class="card-body" id="environmentData"><pre class="codebox"><?= $error_esc($error_json($env_data)) ?></pre></div>
      </section>

      <section class="card half">
        <div class="card-header">
          <h2>Runtime Context</h2>
          <div class="toolbar"><button type="button" onclick="toggleSection('runtimeContext')">Show / Hide</button></div>
        </div>
        <div class="card-body" id="runtimeContext"><pre class="codebox"><?= $error_esc($error_json($extra_context)) ?></pre></div>
      </section>

      <section class="card">
        <div class="card-header">
          <h2>Framework APP State</h2>
          <div class="toolbar"><button type="button" onclick="toggleSection('frameworkState')">Show / Hide</button></div>
        </div>
        <div class="card-body" id="frameworkState">
          <?php if (!empty($app_state_snapshot)): ?>
            <pre class="codebox"><?= $error_esc($error_json($app_state_snapshot)) ?></pre>
          <?php else: ?>
            <div class="empty">No framework APP state was available when the error occurred.</div>
          <?php endif; ?>
        </div>
      </section>

      <section class="card">
        <div class="card-header">
          <h2>Stack Trace</h2>
          <div class="toolbar"><button type="button" onclick="toggleSection('stackTrace')">Show / Hide</button></div>
        </div>
        <div class="card-body" id="stackTrace">
          <?php if (!empty($trace)): ?>
            <?php foreach ($trace as $index => $frame): ?>
              <?php
                $call = (isset($frame['class']) ? $frame['class'] : '')
                    . (isset($frame['type']) ? $frame['type'] : '')
                    . (isset($frame['function']) ? $frame['function'] : '[unknown]')
                    . '()';
                $location = (isset($frame['file']) ? $frame['file'] : '[internal function]')
                    . (isset($frame['line']) && $frame['line'] !== null ? ':' . $frame['line'] : '');
              ?>
              <div class="stack-line">
                <div class="stack-heading">
                  <div class="stack-number">#<?= $error_esc($index) ?></div>
                  <div>
                    <div class="stack-call"><?= $error_esc($call) ?></div>
                    <div class="stack-location"><?= $error_esc($location) ?></div>
                  </div>
                </div>

                <?php if (!empty($frame['args'])): ?>
                  <div class="stack-args">
                    <pre class="codebox"><?= $error_esc($error_json($frame['args'])) ?></pre>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php elseif (empty($fatal_trace_lines)): ?>
            <div class="empty">No structured stack trace was available for this error.</div>
          <?php endif; ?>

          <?php if (!empty($fatal_trace_lines)): ?>
            <div class="stack-line">
              <div class="stack-call">Fatal error trace reported by PHP</div>
              <pre class="codebox"><?= $error_esc(implode("\n", $fatal_trace_lines)) ?></pre>
            </div>
          <?php endif; ?>
        </div>
      </section>

      <section class="card">
        <div class="card-header">
          <h2>Matched Routes</h2>
          <div class="toolbar"><button type="button" onclick="toggleSection('matchedRoutes')">Show / Hide</button></div>
        </div>
        <div class="card-body" id="matchedRoutes">
          <?php if (!empty($matched_routes)): ?>
            <table>
              <thead>
                <tr><th>Plugin ID</th><th>Route Names</th></tr>
              </thead>
              <tbody>
                <?php foreach ($matched_routes as $plugin => $routes): ?>
                  <?php $routes = is_array($routes) ? array_values($routes) : [$routes]; ?>
                  <?php foreach ($routes as $route_index => $route): ?>
                    <tr>
                      <?php if ($route_index === 0): ?>
                        <th rowspan="<?= $error_esc(count($routes)) ?>"><?= $error_esc($plugin) ?></th>
                      <?php endif; ?>
                      <td><?= $error_esc($error_display($route)) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="empty">No matched routes were recorded before the error occurred.</div>
          <?php endif; ?>
        </div>
      </section>

    </div>

    <div class="footer">
      Generated by <?= $error_esc($app_name) ?> error handler · Request ID: <?= $error_esc($request_id) ?>
    </div>
  </div>

  <script>
    function toggleSection(id){
      const el = document.getElementById(id);
      if(el){
        el.classList.toggle('hidden');
      }
    }
  </script>
</body>
</html>
