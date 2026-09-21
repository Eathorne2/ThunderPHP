<?php

declare(strict_types=1);

namespace ThunderVisualIde;

use ThunderVisualIde\Compiler\ProjectCompiler;
use ThunderVisualIde\FormTheme\FormThemeRegistry;
use ThunderVisualIde\Compiler\ZipReader;
use ThunderVisualIde\Node\NodeRegistry;
use ThunderVisualIde\Marketplace\MarketplaceAssetManager;

final class ApiController
{
    public static function handle(NodeRegistry $registry, ?FormThemeRegistry $formThemes = null): never
    {
        try {
            $request = new \Core\Request();
            $post = $request->post();
            $post = is_array($post) ? $post : [];

            // CSRF verification is intentionally disabled for the visual IDE.
            // The IDE must be usable before an authentication or session system exists.
            // if (!csrf_verify($post, 'thunder_visual_ide')) {
            //     self::json(['ok' => false, 'error' => 'The request token is invalid or expired.'], 419);
            // }

            $action = (string)get_param('action');
            if ($action === 'project-list') {
                self::listProjects();
            }
            if ($action === 'project-load') {
                self::loadProject((string)($post['project_id'] ?? ''));
            }
            if ($action === 'project-delete') {
                self::deleteProject((string)($post['project_id'] ?? ''));
            }
            if ($action === 'library-upload') {
                self::uploadLibraryPackage();
            }
            if ($action === 'project-package-import') {
                self::importProjectPackage();
            }
            if ($action === 'ide-data-export') {
                self::exportIdeData($post);
            }
            if ($action === 'ide-data-import') {
                self::importIdeData();
            }
            if ($action === 'ide-data-clear') {
                self::clearIdeData();
            }
            if ($action === 'marketplace-assets-list') {
                self::marketplaceAssetsList();
            }
            if ($action === 'marketplace-theme-save') {
                self::marketplaceSave('theme', $post);
            }
            if ($action === 'marketplace-snippet-save') {
                self::marketplaceSave('snippet', $post);
            }
            if ($action === 'marketplace-theme-delete') {
                self::marketplaceDelete('theme', (string) ($post['asset_id'] ?? ''));
            }
            if ($action === 'marketplace-snippet-delete') {
                self::marketplaceDelete('snippet', (string) ($post['asset_id'] ?? ''));
            }
            if ($action === 'marketplace-theme-export') {
                self::marketplaceExport('theme', (string) ($post['asset_id'] ?? ''));
            }
            if ($action === 'marketplace-snippet-export') {
                self::marketplaceExport('snippet', (string) ($post['asset_id'] ?? ''));
            }
            if ($action === 'marketplace-package-import') {
                self::marketplaceImport((string) ($post['asset_type'] ?? ''));
            }

            $project = self::projectFromPost($post);
            if ($action === 'project-package-export') {
                self::exportProjectPackage(
                    $project,
                    !empty($post['include_assets']),
                    !empty($post['include_libraries'])
                );
            }
            if ($action === 'project-save') {
                self::saveProject(
                    $project,
                    (string)($post['project_name'] ?? ''),
                    (string)($post['project_id'] ?? '')
                );
            }

            $result = (new ProjectCompiler($registry, $formThemes))->compile($project);
            if ($action === 'validate') {
                self::json([
                    'ok' => $result['errors'] === [],
                    'errors' => $result['errors'],
                    'warnings' => $result['warnings'],
                    'summary' => $result['summary'],
                ], $result['errors'] === [] ? 200 : 422);
            }

            if ($action === 'preview') {
                self::json([
                    'ok' => $result['errors'] === [],
                    'errors' => $result['errors'],
                    'warnings' => $result['warnings'],
                    'summary' => $result['summary'],
                    'files' => array_map(
                        static fn(string $path, string $content): array => [
                            'path' => $path,
                            'content' => (str_contains($content, "\0") || preg_match('//u', $content) !== 1) ? '[binary asset]' : $content,
                        ],
                        array_keys($result['files']),
                        array_values($result['files'])
                    ),
                ], $result['errors'] === [] ? 200 : 422);
            }

            if ($action === 'build') {
                if ($result['errors'] !== []) {
                    self::json(['ok' => false, 'errors' => $result['errors'], 'warnings' => $result['warnings']], 422);
                }
                $pluginId = (string)($result['summary']['plugin_id'] ?? 'generated-plugin');
                $zip = ZipWriter::create($result['files'], $pluginId);
                $filename = preg_replace('/[^a-z0-9._-]+/i', '-', $pluginId) . '.zip';
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-Length: ' . strlen($zip));
                header('Cache-Control: no-store');
                echo $zip;
                exit;
            }

            if ($action === 'test') {
                if ($result['errors'] !== []) {
                    self::json(['ok' => false, 'errors' => $result['errors'], 'warnings' => $result['warnings']], 422);
                }
                self::deployTestBuild(
                    $result,
                    !empty($post['override_existing']),
                    (string) ($post['test_route_name'] ?? ''),
                    (string) ($post['test_route_path'] ?? ''),
                    (string) ($post['test_route_method'] ?? '')
                );
            }

            if (in_array($action, ['migration-run', 'migration-rollback', 'migration-status'], true)) {
                if ($result['errors'] !== []) {
                    self::json([
                        'ok' => false,
                        'errors' => $result['errors'],
                        'warnings' => $result['warnings'],
                    ], 422);
                }

                $pluginId = self::stageGeneratedPlugin(
                    $result,
                    !empty($post['override_existing'])
                );
                $command = match ($action) {
                    'migration-rollback' => 'migrate:rollback',
                    'migration-status' => 'migrate:status',
                    default => 'migrate',
                };
                $migrationResult = self::runMigrationCommand($pluginId, $command);
                self::json([
                    'ok' => $migrationResult['ok'],
                    'plugin_id' => $pluginId,
                    'output' => $migrationResult['output'],
                    'warnings' => $result['warnings'],
                ], $migrationResult['ok'] ? 200 : 500);
            }

            self::json(['ok' => false, 'error' => 'Unknown IDE action.'], 404);
        } catch (\JsonException $exception) {
            self::json(['ok' => false, 'error' => 'Invalid project JSON: ' . $exception->getMessage()], 422);
        } catch (\Throwable $exception) {
            self::json(['ok' => false, 'error' => 'IDE request failed: ' . $exception->getMessage()], 500);
        }
    }

    private static function marketplaceManager(): MarketplaceAssetManager
    {
        return new MarketplaceAssetManager(
            plugin_path('form-themes'),
            plugin_path('storage/marketplace/form-themes'),
            plugin_path('html-snippets'),
            plugin_path('storage/marketplace/html-snippets')
        );
    }

    private static function marketplaceAssetsList(): never
    {
        self::json(['ok' => true] + self::marketplaceManager()->studioData());
    }

    /** @param array<string,mixed> $post */
    private static function marketplaceSave(string $type, array $post): never
    {
        try {
            $payload = json_decode((string) ($post['asset_json'] ?? ''), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($payload)) {
                self::json(['ok' => false, 'error' => 'The marketplace asset payload is invalid.'], 422);
            }
            $manager = self::marketplaceManager();
            $result = $type === 'theme'
                ? $manager->saveTheme($payload)
                : $manager->saveSnippet($payload);
            self::json(['ok' => true, 'asset_type' => $type, 'result' => $result] + $manager->studioData());
        } catch (\JsonException|\RuntimeException $exception) {
            self::json(['ok' => false, 'error' => $exception->getMessage()], 422);
        }
    }

    private static function marketplaceDelete(string $type, string $id): never
    {
        try {
            $manager = self::marketplaceManager();
            if ($type === 'theme') {
                $manager->deleteTheme($id);
            } else {
                $manager->deleteSnippet($id);
            }
            self::json(['ok' => true, 'asset_type' => $type, 'deleted_id' => $id] + $manager->studioData());
        } catch (\RuntimeException $exception) {
            self::json(['ok' => false, 'error' => $exception->getMessage()], 422);
        }
    }

    private static function marketplaceExport(string $type, string $id): never
    {
        try {
            $manager = self::marketplaceManager();
            $zip = $type === 'theme' ? $manager->exportTheme($id) : $manager->exportSnippet($id);
            $safeId = preg_replace('/[^a-z0-9._-]+/i', '-', $id) ?: $type;
            $filename = $safeId . ($type === 'theme' ? '.form-theme.zip' : '.html-snippet.zip');
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($zip));
            header('Cache-Control: no-store');
            echo $zip;
            exit;
        } catch (\RuntimeException $exception) {
            self::json(['ok' => false, 'error' => $exception->getMessage()], 422);
        }
    }

    private static function marketplaceImport(string $expectedType): never
    {
        try {
            $file = $_FILES['marketplace_archive'] ?? null;
            if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                self::json(['ok' => false, 'error' => 'Select a readable marketplace ZIP package.'], 422);
            }
            $temporary = (string) ($file['tmp_name'] ?? '');
            $name = trim((string) ($file['name'] ?? 'marketplace-package.zip'));
            $size = (int) ($file['size'] ?? 0);
            if ($temporary === '' || !is_uploaded_file($temporary)) {
                self::json(['ok' => false, 'error' => 'The marketplace package upload could not be verified.'], 422);
            }
            if (!str_ends_with(strtolower($name), '.zip')) {
                self::json(['ok' => false, 'error' => 'Marketplace packages must be ZIP archives.'], 422);
            }
            if ($size <= 0 || $size > 20 * 1024 * 1024) {
                self::json(['ok' => false, 'error' => 'Marketplace packages must be between 1 byte and 20 MB.'], 422);
            }
            $manager = self::marketplaceManager();
            $result = $manager->importArchive(
                (string) file_get_contents($temporary),
                in_array($expectedType, ['theme', 'snippet'], true) ? $expectedType : ''
            );
            self::json(['ok' => true, 'result' => $result] + $manager->studioData());
        } catch (\JsonException|\RuntimeException $exception) {
            self::json(['ok' => false, 'error' => $exception->getMessage()], 422);
        }
    }

    private static function uploadLibraryPackage(): never
    {
        $file = $_FILES['library_archive'] ?? null;
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            self::json(['ok' => false, 'error' => 'Select a readable ZIP archive.'], 422);
        }

        $name = trim((string) ($file['name'] ?? 'library.zip'));
        $temporary = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);
        if ($temporary === '' || !is_uploaded_file($temporary)) {
            self::json(['ok' => false, 'error' => 'The library upload could not be verified.'], 422);
        }
        if (!str_ends_with(strtolower($name), '.zip')) {
            self::json(['ok' => false, 'error' => 'Library packages must be uploaded as ZIP archives.'], 422);
        }
        if ($size <= 0 || $size > 200 * 1024 * 1024) {
            self::json(['ok' => false, 'error' => 'The library ZIP must be between 1 byte and 200 MB.'], 422);
        }

        $directory = rtrim((string) plugin_path('storage/libraries'), '/\\');
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            self::json(['ok' => false, 'error' => 'Unable to create the library package storage directory.'], 500);
        }
        if (!is_writable($directory)) {
            self::json(['ok' => false, 'error' => 'The library package storage directory is not writable.'], 500);
        }

        $id = 'library-' . bin2hex(random_bytes(12));
        $target = $directory . DIRECTORY_SEPARATOR . $id . '.zip';
        if (!move_uploaded_file($temporary, $target)) {
            self::json(['ok' => false, 'error' => 'Unable to store the uploaded library package.'], 500);
        }

        self::json([
            'ok' => true,
            'archive_id' => $id,
            'original_name' => $name,
            'size' => filesize($target) ?: $size,
        ]);
    }

    /** @param array<string,mixed> $project */
    private static function exportProjectPackage(
        array $project,
        bool $includeAssets,
        bool $includeLibraries
    ): never {
        $packageProject = $project;
        unset($packageProject['workspace']);

        $files = [];
        $assetCount = 0;
        $libraryCount = 0;
        $missing = [];

        foreach ($packageProject['graphs'] as &$graph) {
            if (!is_array($graph)) {
                continue;
            }

            $graph['nodes'] = is_array($graph['nodes'] ?? null) ? $graph['nodes'] : [];
            foreach ($graph['nodes'] as &$node) {
                if (!is_array($node)) {
                    continue;
                }

                $type = (string) ($node['type'] ?? '');
                $data = is_array($node['data'] ?? null) ? $node['data'] : [];

                if ($type === 'looks.asset') {
                    if ($includeAssets) {
                        $asset = self::decodeDataUrl((string) ($data['content_base64'] ?? ''));
                        if ($asset['content'] !== '') {
                            $name = self::packageFileName(
                                (string) ($data['original_name'] ?? ''),
                                basename((string) ($data['filename'] ?? 'asset.bin'))
                            );
                            $path = 'resources/assets/'
                                . self::safePackageId((string) ($node['id'] ?? 'asset'))
                                . '/' . $name;
                            $files[$path] = $asset['content'];
                            $data['package_asset_path'] = $path;
                            $data['package_asset_mime'] = $asset['mime'];
                            $data['content_base64'] = '';
                            $assetCount++;
                        } else {
                            $missing[] = 'Asset node ' . (string) ($data['title'] ?? $node['id'] ?? 'unknown')
                                . ' did not contain an uploaded file.';
                        }
                    } else {
                        $data['content_base64'] = '';
                    }
                }

                if ($type === 'libraries.package') {
                    if ($includeLibraries) {
                        $archive = self::libraryArchiveContents($data);
                        if ($archive !== '') {
                            $name = self::packageFileName(
                                (string) ($data['original_name'] ?? ''),
                                'library.zip'
                            );
                            if (!str_ends_with(strtolower($name), '.zip')) {
                                $name .= '.zip';
                            }
                            $path = 'resources/libraries/'
                                . self::safePackageId((string) ($node['id'] ?? 'library'))
                                . '/' . $name;
                            $files[$path] = $archive;
                            $data['package_library_path'] = $path;
                            $data['archive_id'] = '';
                            $data['content_base64'] = '';
                            $libraryCount++;
                        } else {
                            $missing[] = 'Library node ' . (string) ($data['title'] ?? $node['id'] ?? 'unknown')
                                . ' did not contain an available ZIP archive.';
                        }
                    } else {
                        $data['archive_id'] = '';
                        $data['content_base64'] = '';
                    }
                }

                $node['data'] = $data;
            }
            unset($node);
        }
        unset($graph);

        $packageProject['package'] = [
            'format' => 'thunder-visual-ide-project-package',
            'format_version' => 1,
            'created_at' => date(DATE_ATOM),
            'includes_assets' => $includeAssets,
            'includes_libraries' => $includeLibraries,
        ];

        $files['project.thunder.json'] = json_encode(
            $packageProject,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ) ?: '{}';
        $files['manifest.json'] = json_encode([
            'format' => 'thunder-visual-ide-project-package',
            'format_version' => 1,
            'created_at' => date(DATE_ATOM),
            'includes_assets' => $includeAssets,
            'includes_libraries' => $includeLibraries,
            'asset_files' => $assetCount,
            'library_archives' => $libraryCount,
            'notes' => $missing,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';

        $projectId = self::projectPluginId($project);
        $root = $projectId . '-project';
        $zip = ZipWriter::create($files, $root);
        $filename = preg_replace('/[^a-z0-9._-]+/i', '-', $projectId) . '.thunder-project.zip';

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($zip));
        header('Cache-Control: no-store');
        echo $zip;
        exit;
    }

    private static function importProjectPackage(): never
    {
        $file = $_FILES['project_package'] ?? null;
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            self::json(['ok' => false, 'error' => 'Select a readable Thunder project ZIP package.'], 422);
        }

        $name = trim((string) ($file['name'] ?? 'project.zip'));
        $temporary = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);
        if ($temporary === '' || !is_uploaded_file($temporary)) {
            self::json(['ok' => false, 'error' => 'The project package upload could not be verified.'], 422);
        }
        if (!str_ends_with(strtolower($name), '.zip')) {
            self::json(['ok' => false, 'error' => 'Project packages must be ZIP archives.'], 422);
        }
        if ($size <= 0 || $size > 250 * 1024 * 1024) {
            self::json(['ok' => false, 'error' => 'The project package must be between 1 byte and 250 MB.'], 422);
        }

        $archive = (string) file_get_contents($temporary);
        $files = ZipReader::files($archive, true);
        $projectJson = $files['project.thunder.json'] ?? '';
        if ($projectJson === '') {
            self::json(['ok' => false, 'error' => 'The ZIP does not contain project.thunder.json.'], 422);
        }

        $project = json_decode($projectJson, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($project) || (int) ($project['schema_version'] ?? 0) !== 4) {
            self::json(['ok' => false, 'error' => 'The packaged project is not schema version 4.'], 422);
        }

        $assetCount = 0;
        $libraryCount = 0;
        $warnings = [];

        foreach ($project['graphs'] as &$graph) {
            if (!is_array($graph)) {
                continue;
            }

            $graph['nodes'] = is_array($graph['nodes'] ?? null) ? $graph['nodes'] : [];
            foreach ($graph['nodes'] as &$node) {
                if (!is_array($node)) {
                    continue;
                }

                $type = (string) ($node['type'] ?? '');
                $data = is_array($node['data'] ?? null) ? $node['data'] : [];

                if ($type === 'looks.asset' && !empty($data['package_asset_path'])) {
                    $path = self::safeRelativePath((string) $data['package_asset_path']);
                    if ($path !== '' && array_key_exists($path, $files)) {
                        $mime = trim((string) ($data['package_asset_mime'] ?? '')) ?: 'application/octet-stream';
                        $data['content_base64'] = 'data:' . $mime . ';base64,' . base64_encode($files[$path]);
                        $assetCount++;
                    } else {
                        $warnings[] = 'A packaged asset file was missing: ' . $path;
                    }
                    unset($data['package_asset_path'], $data['package_asset_mime']);
                }

                if ($type === 'libraries.package' && !empty($data['package_library_path'])) {
                    $path = self::safeRelativePath((string) $data['package_library_path']);
                    if ($path !== '' && array_key_exists($path, $files)) {
                        $archiveId = 'library-' . bin2hex(random_bytes(12));
                        $target = self::librariesDirectory() . DIRECTORY_SEPARATOR . $archiveId . '.zip';
                        if (file_put_contents($target, $files[$path], LOCK_EX) === false) {
                            self::json(['ok' => false, 'error' => 'Unable to restore a packaged library archive.'], 500);
                        }
                        $data['archive_id'] = $archiveId;
                        $data['archive_size'] = strlen($files[$path]);
                        $data['content_base64'] = '';
                        $libraryCount++;
                    } else {
                        $warnings[] = 'A packaged library archive was missing: ' . $path;
                    }
                    unset($data['package_library_path']);
                }

                $node['data'] = $data;
            }
            unset($node);
        }
        unset($graph);

        unset($project['workspace'], $project['package']);
        $project['active_graph_id'] = 'main';

        self::json([
            'ok' => true,
            'project' => $project,
            'restored_assets' => $assetCount,
            'restored_libraries' => $libraryCount,
            'warnings' => $warnings,
        ]);
    }

    /** @param array<string,mixed> $post */
    private static function exportIdeData(array $post): never
    {
        $files = [];
        $projectCount = 0;
        $libraryCount = 0;
        $customThemeCount = 0;
        $customSnippetCount = 0;

        foreach (glob(self::projectsDirectory() . DIRECTORY_SEPARATOR . '*.json') ?: [] as $file) {
            $name = basename($file);
            $contents = (string) file_get_contents($file);
            $record = json_decode($contents, true);
            if (!is_array($record) || !is_array($record['project'] ?? null)) {
                continue;
            }
            $files['storage/projects/' . $name] = $contents;
            $projectCount++;
        }

        foreach (glob(self::librariesDirectory() . DIRECTORY_SEPARATOR . '*.zip') ?: [] as $file) {
            $name = basename($file);
            if (preg_match('/^library-[a-zA-Z0-9_-]+\.zip$/', $name) !== 1) {
                continue;
            }
            $files['storage/libraries/' . $name] = (string) file_get_contents($file);
            $libraryCount++;
        }

        $customThemeCount = self::appendMarketplacePackagesToBackup(
            plugin_path('storage/marketplace/form-themes'),
            'storage/marketplace/form-themes',
            $files
        );
        $customSnippetCount = self::appendMarketplacePackagesToBackup(
            plugin_path('storage/marketplace/html-snippets'),
            'storage/marketplace/html-snippets',
            $files
        );

        $presets = json_decode((string) ($post['presets'] ?? '[]'), true);
        $presets = is_array($presets) ? array_values($presets) : [];
        $files['browser/presets.json'] = json_encode(
            $presets,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ) ?: '[]';

        foreach (['recovery_project' => 'browser/recovery-project.thunder.json', 'project' => 'browser/current-project.thunder.json'] as $field => $path) {
            $value = trim((string) ($post[$field] ?? ''));
            if ($value === '') {
                continue;
            }
            $project = json_decode($value, true);
            if (is_array($project) && (int) ($project['schema_version'] ?? 0) === 4) {
                $files[$path] = json_encode(
                    $project,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                ) ?: '{}';
            }
        }

        $files['manifest.json'] = json_encode([
            'format' => 'thunder-visual-ide-data',
            'format_version' => 1,
            'created_at' => date(DATE_ATOM),
            'ide_version' => self::ideVersion(),
            'saved_projects' => $projectCount,
            'library_archives' => $libraryCount,
            'custom_form_themes' => $customThemeCount,
            'custom_html_snippets' => $customSnippetCount,
            'presets' => count($presets),
            'includes_recovery_project' => isset($files['browser/recovery-project.thunder.json']),
            'includes_current_project' => isset($files['browser/current-project.thunder.json']),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';

        $zip = ZipWriter::create($files, 'thunder-visual-ide-data');
        $filename = 'thunder-visual-ide-data-' . date('Y-m-d-His') . '.zip';
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($zip));
        header('Cache-Control: no-store');
        echo $zip;
        exit;
    }

    private static function importIdeData(): never
    {
        $file = $_FILES['ide_data_archive'] ?? null;
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            self::json(['ok' => false, 'error' => 'Select a readable Thunder Visual IDE data ZIP.'], 422);
        }

        $name = trim((string) ($file['name'] ?? 'ide-data.zip'));
        $temporary = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);
        if ($temporary === '' || !is_uploaded_file($temporary)) {
            self::json(['ok' => false, 'error' => 'The IDE data upload could not be verified.'], 422);
        }
        if (!str_ends_with(strtolower($name), '.zip')) {
            self::json(['ok' => false, 'error' => 'IDE data backups must be ZIP archives.'], 422);
        }
        if ($size <= 0 || $size > 500 * 1024 * 1024) {
            self::json(['ok' => false, 'error' => 'The IDE data ZIP must be between 1 byte and 500 MB.'], 422);
        }

        $files = ZipReader::files((string) file_get_contents($temporary), true);
        $manifest = json_decode((string) ($files['manifest.json'] ?? ''), true);
        if (!is_array($manifest) || ($manifest['format'] ?? '') !== 'thunder-visual-ide-data') {
            self::json(['ok' => false, 'error' => 'The ZIP is not a Thunder Visual IDE data backup.'], 422);
        }
        if ((int) ($manifest['format_version'] ?? 0) !== 1) {
            self::json(['ok' => false, 'error' => 'This IDE data backup version is not supported.'], 422);
        }

        $projects = [];
        $libraries = [];
        $customThemes = [];
        $customSnippets = [];
        foreach ($files as $path => $contents) {
            if (preg_match('#^storage/projects/([a-zA-Z0-9_-]+\.json)$#', $path, $matches) === 1) {
                $record = json_decode($contents, true);
                if (!is_array($record) || !is_array($record['project'] ?? null)
                    || (int) ($record['project']['schema_version'] ?? 0) !== 4) {
                    self::json(['ok' => false, 'error' => 'A saved project in the backup is invalid: ' . $matches[1]], 422);
                }
                $projects[$matches[1]] = $contents;
            }
            if (preg_match('#^storage/libraries/(library-[a-zA-Z0-9_-]+\.zip)$#', $path, $matches) === 1) {
                // Parse once before replacing existing data so corrupt nested archives are rejected safely.
                ZipReader::files($contents, false);
                $libraries[$matches[1]] = $contents;
            }
            if (preg_match('#^storage/marketplace/form-themes/([a-z0-9-]+)/(.+)$#', $path, $matches) === 1) {
                $customThemes[$matches[1]][$matches[2]] = $contents;
            }
            if (preg_match('#^storage/marketplace/html-snippets/([a-z0-9-]+)/(.+)$#', $path, $matches) === 1) {
                $customSnippets[$matches[1]][$matches[2]] = $contents;
            }
        }
        self::validateMarketplaceBackupPackages($customThemes, 'theme.json', 'theme');
        self::validateMarketplaceBackupPackages($customSnippets, 'snippet.json', 'snippet');

        $presets = json_decode((string) ($files['browser/presets.json'] ?? '[]'), true);
        if (!is_array($presets)) {
            self::json(['ok' => false, 'error' => 'The presets file in the backup is invalid.'], 422);
        }
        $presets = array_values(array_filter($presets, static fn (mixed $preset): bool =>
            is_array($preset) && is_array($preset['nodes'] ?? null)
        ));

        $recoveryProject = self::backupProject($files['browser/recovery-project.thunder.json'] ?? '');
        $currentProject = self::backupProject($files['browser/current-project.thunder.json'] ?? '');

        self::clearStorageDirectory(self::projectsDirectory());
        self::clearStorageDirectory(self::librariesDirectory());
        self::clearStorageDirectory(plugin_path('storage/marketplace/form-themes'));
        self::clearStorageDirectory(plugin_path('storage/marketplace/html-snippets'));

        foreach ($projects as $filename => $contents) {
            if (file_put_contents(self::projectsDirectory() . DIRECTORY_SEPARATOR . $filename, $contents, LOCK_EX) === false) {
                self::json(['ok' => false, 'error' => 'Unable to restore saved project ' . $filename . '.'], 500);
            }
        }
        foreach ($libraries as $filename => $contents) {
            if (file_put_contents(self::librariesDirectory() . DIRECTORY_SEPARATOR . $filename, $contents, LOCK_EX) === false) {
                self::json(['ok' => false, 'error' => 'Unable to restore library archive ' . $filename . '.'], 500);
            }
        }
        self::restoreMarketplaceBackupPackages(plugin_path('storage/marketplace/form-themes'), $customThemes);
        self::restoreMarketplaceBackupPackages(plugin_path('storage/marketplace/html-snippets'), $customSnippets);

        self::json([
            'ok' => true,
            'projects' => count($projects),
            'libraries' => count($libraries),
            'custom_form_themes' => count($customThemes),
            'custom_html_snippets' => count($customSnippets),
            'presets' => $presets,
            'recovery_project' => $recoveryProject,
            'current_project' => $currentProject,
        ]);
    }

    private static function clearIdeData(): never
    {
        $projects = self::clearStorageDirectory(self::projectsDirectory());
        $libraries = self::clearStorageDirectory(self::librariesDirectory());
        $themes = self::clearStorageDirectory(plugin_path('storage/marketplace/form-themes'));
        $snippets = self::clearStorageDirectory(plugin_path('storage/marketplace/html-snippets'));
        self::json([
            'ok' => true,
            'deleted_projects' => $projects,
            'deleted_libraries' => $libraries,
            'deleted_custom_form_themes' => $themes,
            'deleted_custom_html_snippets' => $snippets,
        ]);
    }

    /** @param array<string,string> $files */
    private static function appendMarketplacePackagesToBackup(string $root, string $prefix, array &$files): int
    {
        if (!is_dir($root)) {
            return 0;
        }
        $count = 0;
        foreach (new \FilesystemIterator($root, \FilesystemIterator::SKIP_DOTS) as $package) {
            if (!$package->isDir() || $package->isLink() || preg_match('/^[a-z0-9-]+$/', $package->getFilename()) !== 1) {
                continue;
            }
            $count++;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($package->getPathname(), \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $item) {
                if (!$item->isFile() || $item->isLink() || $item->getFilename() === '.gitkeep') {
                    continue;
                }
                $relative = substr($item->getPathname(), strlen($package->getPathname()) + 1);
                $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
                if (str_contains($relative, '..')) {
                    continue;
                }
                $files[$prefix . '/' . $package->getFilename() . '/' . $relative] = (string) file_get_contents($item->getPathname());
            }
        }
        return $count;
    }

    /** @param array<string,array<string,string>> $packages */
    private static function validateMarketplaceBackupPackages(array $packages, string $definitionFile, string $label): void
    {
        foreach ($packages as $folder => $packageFiles) {
            if (!isset($packageFiles[$definitionFile])) {
                self::json(['ok' => false, 'error' => "A custom {$label} package is missing {$definitionFile}: {$folder}"], 422);
            }
            $definition = json_decode((string) $packageFiles[$definitionFile], true);
            if (!is_array($definition)) {
                self::json(['ok' => false, 'error' => "A custom {$label} package has invalid JSON: {$folder}"], 422);
            }
            $id = strtolower(trim((string) ($definition['id'] ?? '')));
            $id = trim((string) preg_replace('/[^a-z0-9]+/', '-', $id), '-');
            if ($id === '' || $id !== $folder) {
                self::json(['ok' => false, 'error' => "A custom {$label} package ID does not match its folder: {$folder}"], 422);
            }
        }
    }

    /** @param array<string,array<string,string>> $packages */
    private static function restoreMarketplaceBackupPackages(string $root, array $packages): void
    {
        if (!is_dir($root) && !mkdir($root, 0775, true) && !is_dir($root)) {
            self::json(['ok' => false, 'error' => 'Unable to create marketplace storage directory.'], 500);
        }
        foreach ($packages as $folder => $packageFiles) {
            $directory = rtrim($root, '/\\') . DIRECTORY_SEPARATOR . $folder;
            if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
                self::json(['ok' => false, 'error' => 'Unable to restore marketplace package ' . $folder . '.'], 500);
            }
            foreach ($packageFiles as $relative => $contents) {
                $relative = str_replace('\\', '/', $relative);
                $parts = array_values(array_filter(explode('/', $relative), static fn (string $part): bool => $part !== '' && $part !== '.'));
                if ($parts === [] || in_array('..', $parts, true)) {
                    self::json(['ok' => false, 'error' => 'A marketplace backup contains an unsafe file path.'], 422);
                }
                $target = $directory . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
                if (!is_dir(dirname($target)) && !mkdir(dirname($target), 0775, true) && !is_dir(dirname($target))) {
                    self::json(['ok' => false, 'error' => 'Unable to create marketplace package subdirectory.'], 500);
                }
                if (file_put_contents($target, $contents, LOCK_EX) === false) {
                    self::json(['ok' => false, 'error' => 'Unable to restore marketplace package file ' . $relative . '.'], 500);
                }
            }
        }
    }

    /** @return array<string,mixed>|null */
    private static function backupProject(string $json): ?array
    {
        if (trim($json) === '') {
            return null;
        }
        $project = json_decode($json, true);
        if (!is_array($project) || (int) ($project['schema_version'] ?? 0) !== 4) {
            self::json(['ok' => false, 'error' => 'A browser project in the backup is invalid.'], 422);
        }
        return $project;
    }

    private static function clearStorageDirectory(string $directory): int
    {
        $deleted = 0;
        if (!is_dir($directory)) {
            return $deleted;
        }
        foreach (new \FilesystemIterator($directory, \FilesystemIterator::SKIP_DOTS) as $item) {
            if ($item->getFilename() === '.gitkeep') {
                continue;
            }
            $path = $item->getPathname();
            if ($item->isLink() || $item->isFile()) {
                if (@unlink($path)) {
                    $deleted++;
                }
                continue;
            }
            if ($item->isDir()) {
                self::deleteDirectory($path);
                if (!is_dir($path)) {
                    $deleted++;
                }
            }
        }
        return $deleted;
    }

    /** @return array{mime:string,content:string} */
    private static function decodeDataUrl(string $value): array
    {
        if ($value === '') {
            return ['mime' => 'application/octet-stream', 'content' => ''];
        }

        $mime = 'application/octet-stream';
        $encoded = $value;
        if (preg_match('/^data:([^;,]+);base64,(.*)$/s', $value, $matches) === 1) {
            $mime = trim((string) $matches[1]) ?: $mime;
            $encoded = (string) $matches[2];
        } elseif (str_contains($value, ',')) {
            $encoded = explode(',', $value, 2)[1];
        }

        $content = base64_decode($encoded, true);
        return [
            'mime' => $mime,
            'content' => $content === false ? '' : $content,
        ];
    }

    /** @param array<string,mixed> $data */
    private static function libraryArchiveContents(array $data): string
    {
        $archiveId = self::safePackageId((string) ($data['archive_id'] ?? ''));
        if ($archiveId !== '') {
            $path = self::librariesDirectory() . DIRECTORY_SEPARATOR . $archiveId . '.zip';
            if (is_file($path)) {
                return (string) file_get_contents($path);
            }
        }

        return self::decodeDataUrl((string) ($data['content_base64'] ?? ''))['content'];
    }

    private static function librariesDirectory(): string
    {
        $directory = rtrim((string) plugin_path('storage/libraries'), '/\\');
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            self::json(['ok' => false, 'error' => 'Unable to create the library package storage directory.'], 500);
        }
        if (!is_writable($directory)) {
            self::json(['ok' => false, 'error' => 'The library package storage directory is not writable.'], 500);
        }
        return $directory;
    }

    /** @param array<string,mixed> $project */
    private static function projectPluginId(array $project): string
    {
        foreach ((array) ($project['graphs']['main']['nodes'] ?? []) as $node) {
            if (is_array($node) && ($node['type'] ?? '') === 'project.plugin') {
                $id = self::safePackageId((string) ($node['data']['id'] ?? ''));
                if ($id !== '') {
                    return strtolower($id);
                }
            }
        }
        return 'thunder-project';
    }

    private static function packageFileName(string $preferred, string $fallback): string
    {
        $name = basename(str_replace('\\', '/', trim($preferred)));
        if ($name === '' || $name === '.' || $name === '..') {
            $name = basename(str_replace('\\', '/', trim($fallback)));
        }
        $name = preg_replace('/[^a-zA-Z0-9._ -]+/', '-', $name) ?: 'file.bin';
        return trim($name) ?: 'file.bin';
    }

    private static function safePackageId(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9_-]+/', '-', trim($value)) ?: '';
    }

    /** @param array<string,mixed> $post @return array<string,mixed> */
    private static function projectFromPost(array $post): array
    {
        $project = json_decode((string)($post['project'] ?? ''), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($project) || (int)($project['schema_version'] ?? 0) !== 4) {
            self::json(['ok' => false, 'error' => 'The project payload is invalid or is not schema version 4.'], 422);
        }
        return $project;
    }

    private static function projectsDirectory(): string
    {
        $directory = rtrim((string)plugin_path('storage/projects'), '/\\');
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            self::json(['ok' => false, 'error' => 'Unable to create the IDE project storage directory.'], 500);
        }
        if (!is_writable($directory)) {
            self::json(['ok' => false, 'error' => 'The IDE project storage directory is not writable.'], 500);
        }
        return $directory;
    }

    private static function projectFile(string $projectId): string
    {
        $projectId = self::safeProjectId($projectId);
        if ($projectId === '') {
            self::json(['ok' => false, 'error' => 'A valid saved-project ID is required.'], 422);
        }
        return self::projectsDirectory() . DIRECTORY_SEPARATOR . $projectId . '.json';
    }

    private static function safeProjectId(string $projectId): string
    {
        return preg_replace('/[^a-zA-Z0-9_-]+/', '', trim($projectId)) ?: '';
    }

    /** @param array<string,mixed> $project */
    private static function saveProject(array $project, string $name, string $projectId): never
    {
        $name = trim($name);
        if ($name === '') {
            $pluginNode = null;
            foreach ((array)($project['graphs']['main']['nodes'] ?? []) as $node) {
                if (is_array($node) && ($node['type'] ?? '') === 'project.plugin') {
                    $pluginNode = $node;
                    break;
                }
            }
            $name = trim((string)($pluginNode['data']['name'] ?? 'Untitled Project')) ?: 'Untitled Project';
        }

        $projectId = self::safeProjectId($projectId);
        if ($projectId === '') {
            $base = strtolower(trim((string)preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-')) ?: 'project';
            $projectId = $base . '-' . bin2hex(random_bytes(5));
        }

        $file = self::projectFile($projectId);
        $createdAt = date(DATE_ATOM);
        if (is_file($file)) {
            $existing = json_decode((string)file_get_contents($file), true);
            if (is_array($existing) && !empty($existing['created_at'])) {
                $createdAt = (string)$existing['created_at'];
            }
        }

        $project['workspace'] = [
            'project_id' => $projectId,
            'project_name' => $name,
        ];
        $record = [
            'id' => $projectId,
            'name' => $name,
            'created_at' => $createdAt,
            'modified_at' => date(DATE_ATOM),
            'project' => $project,
        ];
        $encoded = json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($encoded === false || file_put_contents($file, $encoded, LOCK_EX) === false) {
            self::json(['ok' => false, 'error' => 'Unable to save the project.'], 500);
        }
        self::json(['ok' => true, 'project' => $project, 'record' => self::projectSummary($record)]);
    }

    private static function listProjects(): never
    {
        $projects = [];
        foreach (glob(self::projectsDirectory() . DIRECTORY_SEPARATOR . '*.json') ?: [] as $file) {
            $record = json_decode((string)file_get_contents($file), true);
            if (!is_array($record) || !is_array($record['project'] ?? null)) {
                continue;
            }
            $projects[] = self::projectSummary($record);
        }
        usort($projects, static fn(array $a, array $b): int => strcmp((string)$b['modified_at'], (string)$a['modified_at']));
        self::json(['ok' => true, 'projects' => $projects]);
    }

    private static function loadProject(string $projectId): never
    {
        $file = self::projectFile($projectId);
        if (!is_file($file)) {
            self::json(['ok' => false, 'error' => 'The saved project was not found.'], 404);
        }
        $record = json_decode((string)file_get_contents($file), true);
        if (!is_array($record) || !is_array($record['project'] ?? null)) {
            self::json(['ok' => false, 'error' => 'The saved project file is invalid.'], 500);
        }
        self::json(['ok' => true, 'project' => $record['project'], 'record' => self::projectSummary($record)]);
    }

    private static function deleteProject(string $projectId): never
    {
        $file = self::projectFile($projectId);
        if (!is_file($file)) {
            self::json(['ok' => false, 'error' => 'The saved project was not found.'], 404);
        }
        if (!unlink($file)) {
            self::json(['ok' => false, 'error' => 'Unable to delete the saved project.'], 500);
        }
        self::json(['ok' => true]);
    }

    /** @param array<string,mixed> $record @return array<string,string> */
    private static function projectSummary(array $record): array
    {
        return [
            'id' => (string)($record['id'] ?? ''),
            'name' => (string)($record['name'] ?? 'Untitled Project'),
            'created_at' => (string)($record['created_at'] ?? ''),
            'modified_at' => (string)($record['modified_at'] ?? ''),
        ];
    }

    /** @param array{files:array<string,string>,summary:array<string,mixed>,warnings:list<string>} $result */
    private static function deployTestBuild(
        array $result,
        bool $overrideExisting,
        string $preferredRouteName = '',
        string $preferredRoutePath = '',
        string $preferredRouteMethod = ''
    ): never {
        $pluginId = self::stageGeneratedPlugin($result, $overrideExisting);

        self::json([
            'ok' => true,
            'warnings' => $result['warnings'],
            'folder' => $pluginId,
            'url' => self::testUrl(
                $result['files'],
                $preferredRouteName,
                $preferredRoutePath,
                $preferredRouteMethod
            ),
        ]);
    }

    /**
     * @param array{files:array<string,string>,summary:array<string,mixed>,warnings:list<string>} $result
     */
    private static function stageGeneratedPlugin(array $result, bool $overrideExisting): string
    {
        $pluginId = preg_replace(
            '/[^a-z0-9_-]+/i',
            '-',
            (string) ($result['summary']['plugin_id'] ?? 'generated-plugin')
        ) ?: 'generated-plugin';
        $pluginsRoot = dirname((string) plugin_path('plugin.php'), 2);
        if (!is_dir($pluginsRoot) || !is_writable($pluginsRoot)) {
            self::json(['ok' => false, 'error' => 'The ThunderPHP plugins directory is not writable.'], 500);
        }

        $target = $pluginsRoot . DIRECTORY_SEPARATOR . $pluginId;
        $currentPlugin = realpath((string) plugin_path('')) ?: rtrim((string) plugin_path(''), '/\\');
        $targetReal = realpath($target);
        if ($targetReal !== false && rtrim($targetReal, '/\\') === rtrim($currentPlugin, '/\\')) {
            self::json([
                'ok' => false,
                'error' => 'The IDE cannot overwrite its own plugin directory. Change the generated plugin ID.',
            ], 409);
        }

        $marker = $target . DIRECTORY_SEPARATOR . '.tvi-test-build.json';
        if (is_dir($target)) {
            if (!$overrideExisting) {
                $existingType = is_file($marker)
                    ? 'A previous test build'
                    : 'A plugin folder';

                self::json([
                    'ok' => false,
                    'requires_override' => true,
                    'plugin_id' => $pluginId,
                    'error' => "{$existingType} named {$pluginId} is already there. Choose Replace and Continue to update it with the current test build.",
                ], 409);
            }
            self::deleteDirectory($target);
        }

        if (!mkdir($target, 0775, true) && !is_dir($target)) {
            self::json(['ok' => false, 'error' => 'Unable to create the generated plugin directory.'], 500);
        }

        foreach ($result['files'] as $relative => $content) {
            $relative = self::safeRelativePath((string) $relative);
            if ($relative === '') {
                continue;
            }
            $path = $target . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
            $directory = dirname($path);
            if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
                self::deleteDirectory($target);
                self::json(['ok' => false, 'error' => "Unable to create directory for {$relative}."], 500);
            }
            if (file_put_contents($path, (string) $content) === false) {
                self::deleteDirectory($target);
                self::json(['ok' => false, 'error' => "Unable to write {$relative}."], 500);
            }
        }

        file_put_contents($marker, json_encode([
            'created_by' => 'Thunder Visual IDE',
            'plugin_id' => $pluginId,
            'date_created' => date(DATE_ATOM),
            'overrode_existing_plugin' => $overrideExisting,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $pluginId;
    }

    /** @return array{ok:bool,output:string} */
    private static function runMigrationCommand(string $pluginId, string $command): array
    {
        try {
            $rootPath = defined('ROOTPATH') ? rtrim((string) ROOTPATH, '/\\') . DIRECTORY_SEPARATOR : '';
            require_once $rootPath . 'app/thunder/Database.php';
            require_once $rootPath . 'app/models/Migration.php';
            require_once $rootPath . 'app/thunder/MigrationTracker.php';
            require_once $rootPath . 'app/thunder/thunder.php';

            $thunder = new \Thunder\Thunder();
            ob_start();
            $thunder->migrate(['0', $command, $pluginId]);
            $output = trim((string) ob_get_clean());
            $lower = strtolower($output);
            $ok = !str_contains($lower, 'no direct migration runner')
                && !str_contains($lower, 'fatal error')
                && !str_contains($lower, 'migration failed');

            return [
                'ok' => $ok,
                'output' => $output !== '' ? $output : self::migrationFallbackMessage($command),
            ];
        } catch (\Throwable $exception) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            return [
                'ok' => false,
                'output' => 'Migration command failed: ' . $exception->getMessage(),
            ];
        }
    }

    private static function migrationFallbackMessage(string $command): string
    {
        return match ($command) {
            'migrate:rollback' => 'Migration rollback completed.',
            'migrate:status' => 'Migration status command completed.',
            default => 'Migrations completed.',
        };
    }

    /** @param array<string,string> $files */
    private static function testUrl(
        array $files,
        string $preferredRouteName = '',
        string $preferredRoutePath = '',
        string $preferredRouteMethod = ''
    ): string {
        $config = json_decode((string) ($files['config.json'] ?? '{}'), true);
        $routes = is_array($config['routes']['routes'] ?? null) ? $config['routes']['routes'] : [];
        $selected = '/';

        if (strtoupper($preferredRouteMethod) === 'GET') {
            foreach ($routes as $route) {
                $routeName = (string) ($route['name'] ?? '');
                $routePath = (string) ($route['pattern'] ?? '/');
                if (
                    ($preferredRouteName !== '' && $routeName === $preferredRouteName)
                    || ($preferredRoutePath !== '' && $routePath === $preferredRoutePath)
                ) {
                    $selected = $routePath;
                    break;
                }
            }
        }

        if ($selected === '/') {
            foreach ($routes as $route) {
                if (strtoupper((string) ($route['method'] ?? 'GET')) !== 'GET') {
                    continue;
                }
                $selected = (string) ($route['pattern'] ?? '/');
                break;
            }
        }

        $selected = preg_replace('/\{[^}]+\}/', '1', $selected) ?: '/';
        return rtrim((string) ROOT, '/') . '/' . ltrim($selected, '/');
    }

    private static function ideVersion(): string
    {
        $configPath = dirname(__DIR__, 2) . '/config.json';
        if (!is_file($configPath)) {
            return '';
        }

        $config = json_decode((string) file_get_contents($configPath), true);
        return is_array($config) ? trim((string) ($config['version'] ?? '')) : '';
    }

    private static function safeRelativePath(string $path): string
    {
        $parts = [];
        foreach (explode('/', str_replace('\\', '/', $path)) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                array_pop($parts);
                continue;
            }
            $parts[] = $part;
        }
        return implode('/', $parts);
    }

    private static function deleteDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }
        $items = new \FilesystemIterator($directory, \FilesystemIterator::SKIP_DOTS);
        foreach ($items as $item) {
            $path = $item->getPathname();
            if ($item->isLink() || $item->isFile()) {
                @unlink($path);
            } elseif ($item->isDir()) {
                self::deleteDirectory($path);
            }
        }
        @rmdir($directory);
    }

    /** @param array<string,mixed> $payload */
    private static function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
