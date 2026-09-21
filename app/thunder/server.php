<?php

/**
 * Router for PHP's built-in development server.
 *
 * The built-in server does not read Apache .htaccess files, so this script
 * provides ThunderPHP's front-controller routing and its important source-file
 * access restrictions while `php thunder serve` is running.
 */

$root = dirname(__DIR__, 2);
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestPath = is_string($requestPath) ? rawurldecode($requestPath) : '/';
$requestPath = '/' . ltrim(str_replace('\\', '/', $requestPath), '/');
$relativePath = ltrim($requestPath, '/');
$requestedFile = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

// Mirror the access restrictions normally supplied by the root/app/plugin
// .htaccess files. This server is intended for local development only.
$blockedRootFiles = [
    'thunder',
    '.htaccess',
];

$pathSegments = $relativePath === '' ? [] : explode('/', $relativePath);
if (in_array('..', $pathSegments, true)) {
    http_response_code(400);
    echo 'Bad Request';
    return true;
}

if (
    in_array($relativePath, $blockedRootFiles, true)
    || $relativePath === 'thunder'
    || str_starts_with($relativePath, 'thunder/')
    || basename($relativePath) === '.htaccess'
) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

if ($relativePath === 'app' || str_starts_with($relativePath, 'app/')) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

if ($relativePath === 'plugins' || str_starts_with($relativePath, 'plugins/')) {
    $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
    $blockedPluginExtensions = [
        'php', 'phtml', 'phar', 'json', 'sql', 'log', 'env', 'ini',
        'yml', 'yaml', 'sh', 'bat', 'md', 'txt',
    ];

    if (in_array($extension, $blockedPluginExtensions, true)) {
        http_response_code(403);
        echo 'Forbidden';
        return true;
    }
}

// Let the built-in server deliver public static files directly.
if ($relativePath !== '' && is_file($requestedFile)) {
    return false;
}

// Send every non-file request through ThunderPHP's front controller.
$route = trim($requestPath, '/');
$_GET['url'] = $route !== '' ? $route : 'home';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $root . DIRECTORY_SEPARATOR . 'index.php';

chdir($root);
require $root . DIRECTORY_SEPARATOR . 'index.php';

return true;
