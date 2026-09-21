<?php

declare(strict_types=1);

namespace ThunderVisualIde;

require_once plugin_path('src/Compiler/ZipWriter.php');
require_once plugin_path('src/Compiler/ZipReader.php');
require_once plugin_path('src/Compiler/NodeCompilerInterface.php');
require_once plugin_path('src/Node/NodeRegistry.php');
require_once plugin_path('src/FormTheme/FormThemeRegistry.php');
require_once plugin_path('src/Compiler/ViewNodeSupport.php');
require_once plugin_path('src/Compiler/JavaScriptNodeSupport.php');
require_once plugin_path('src/Compiler/ModalNodeSupport.php');
require_once plugin_path('src/Compiler/CompileContext.php');
require_once plugin_path('src/Compiler/ProjectCompiler.php');
require_once plugin_path('src/Http/ApiController.php');
require_once plugin_path('src/Pagination/PaginationTemplateRegistry.php');
require_once plugin_path('src/Html/CssScope.php');
require_once plugin_path('src/Html/HtmlSnippetRegistry.php');
require_once plugin_path('src/Preset/BundledPresetRegistry.php');
require_once plugin_path('src/Tutorial/TutorialRegistry.php');
require_once plugin_path('src/Marketplace/MarketplaceAssetManager.php');

function node_registry(): Node\NodeRegistry
{
    static $registry;
    return $registry ??= new Node\NodeRegistry(plugin_path('nodes'));
}

function form_theme_registry(): FormTheme\FormThemeRegistry
{
    static $registry;
    return $registry ??= new FormTheme\FormThemeRegistry(
        plugin_path('form-themes'),
        plugin_path('storage/marketplace/form-themes')
    );
}

function tutorial_registry(): Tutorial\TutorialRegistry
{
    static $registry;
    return $registry ??= new Tutorial\TutorialRegistry(plugin_path('tutorials'));
}

add_action('view', function (): void {
    // The IDE intentionally works without an authentication or session layer.
    $api_base = ROOT . '/thunder-ide/api';
    $node_definitions = node_registry()->publicDefinitions();
    $pagination_templates = (new Pagination\PaginationTemplateRegistry(
        plugin_path('pagination-templates')
    ))->publicDefinitions();
    $html_snippets = (new Html\HtmlSnippetRegistry(
        plugin_path('html-snippets'),
        plugin_path('storage/marketplace/html-snippets')
    ))->publicDefinitions();
    $bundled_presets = (new Preset\BundledPresetRegistry(
        plugin_path('presets')
    ))->publicDefinitions();
    $form_themes = form_theme_registry()->publicDefinitions();
    $validation_rules_path = plugin_path('validation-rules/rules.json');
    $validation_rules = is_file($validation_rules_path)
        ? json_decode((string) file_get_contents($validation_rules_path), true)
        : [];
    if (!is_array($validation_rules)) {
        $validation_rules = [];
    }
    $sample_projects = [];
    foreach (glob(plugin_path('examples/*.thunder.json')) ?: [] as $sample_path) {
        $sample_project = json_decode((string) file_get_contents($sample_path), true);
        if (!is_array($sample_project)) {
            continue;
        }

        $sample_meta = is_array($sample_project['sample'] ?? null)
            ? $sample_project['sample']
            : [];
        $sample_id = trim((string) ($sample_meta['id'] ?? pathinfo($sample_path, PATHINFO_FILENAME)));
        if ($sample_id === '') {
            continue;
        }

        $sample_projects[] = [
            'id' => $sample_id,
            'name' => trim((string) ($sample_meta['name'] ?? $sample_id)) ?: $sample_id,
            'description' => trim((string) ($sample_meta['description'] ?? '')),
            'level' => trim((string) ($sample_meta['level'] ?? 'Beginner')) ?: 'Beginner',
            'focus' => array_values(array_filter((array) ($sample_meta['focus'] ?? []), 'is_string')),
            'order' => (int) ($sample_meta['order'] ?? 100),
            'project' => $sample_project,
        ];
    }
    usort($sample_projects, static fn (array $a, array $b): int =>
        ($a['order'] <=> $b['order']) ?: strcmp((string) $a['name'], (string) $b['name'])
    );

    // Keep About metadata consistent with the plugin manifest.
    $plugin_config_path = plugin_path('config.json');
    $plugin_info = is_file($plugin_config_path)
        ? json_decode((string) file_get_contents($plugin_config_path), true)
        : [];
    if (!is_array($plugin_info)) {
        $plugin_info = [];
    }

    require current_look('frontend/editor.php');
}, 10, 'thunder_ide.index');

add_action('controller', function (): void {
    ApiController::handle(node_registry(), form_theme_registry());
}, 10, 'thunder_ide.api');


add_action('view', function (): void {
    $tutorial_registry = tutorial_registry();
    $tutorial_index = $tutorial_registry->index();
    $tutorial_categories = $tutorial_registry->categories();
    $tutorials = $tutorial_registry->all();
    require current_look('frontend/tutorials.php');
}, 10, 'thunder_ide.tutorials');

add_action('view', function (): void {
    $tutorial_registry = tutorial_registry();
    $tutorial = $tutorial_registry->find((string) get_param('slug'));
    $tutorial_categories = $tutorial_registry->categories();
    $tutorials = $tutorial_registry->all();
    if ($tutorial === null) {
        http_response_code(404);
    }
    require current_look('frontend/tutorial.php');
}, 10, 'thunder_ide.tutorial');

add_action('controller', function (): void {
    $slug = (string) get_param('slug');
    $zip = tutorial_registry()->companionArchive($slug);
    if ($zip === null) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Tutorial files were not found.';
        exit;
    }

    $filename = preg_replace('/[^a-z0-9._-]+/i', '-', $slug) . '-files.zip';
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($zip));
    header('Cache-Control: no-store');
    echo $zip;
    exit;
}, 10, 'thunder_ide.tutorial_download');

add_action('controller', function (): void {
    $video = tutorial_registry()->localVideo((string) get_param('slug'));
    if ($video === null) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Tutorial video was not found.';
        exit;
    }

    header('Content-Type: ' . $video['mime']);
    header('Content-Length: ' . filesize($video['path']));
    header('Content-Disposition: inline; filename="' . $video['name'] . '"');
    header('Accept-Ranges: bytes');
    readfile($video['path']);
    exit;
}, 10, 'thunder_ide.tutorial_media');
